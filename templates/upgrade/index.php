<div class="max-w-2xl mx-auto py-12">
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-primary-100 rounded-full mb-6">
            <svg class="w-10 h-10 text-primary-600 animate-bounce" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Mise à jour requise</h1>
        <p class="text-lg text-slate-600 mt-4">Le système doit être mis à jour vers la version <span
                class="font-bold text-primary-600">
                <?= $targetVersion ?>
            </span>.</p>
    </div>

    <div class="card shadow-2xl border-0 ring-1 ring-slate-200">
        <div class="p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Migrations en attente</h2>
                <span id="counter" class="text-xs font-bold bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full">
                    0 /
                    <?= count($pending) ?>
                </span>
            </div>

            <div class="space-y-3 mb-8 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                <?php foreach ($pending as $index => $migration): ?>
                    <div id="mig-<?= $index ?>"
                        class="flex items-center justify-between p-3 rounded-lg border border-slate-100 bg-slate-50 transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <div class="status-icon w-2 h-2 rounded-full bg-slate-300"></div>
                            <span class="text-sm font-medium text-slate-700">
                                <?= htmlspecialchars($migration['name']) ?>
                            </span>
                        </div>
                        <span class="status-text text-[10px] font-bold uppercase text-slate-400">En attente</span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="relative pt-1">
                <div class="flex mb-2 items-center justify-between">
                    <div>
                        <span id="progress-text"
                            class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-primary-600 bg-primary-200">
                            Prêt à commencer
                        </span>
                    </div>
                    <div class="text-right">
                        <span id="percentage" class="text-xs font-semibold inline-block text-primary-600">
                            0%
                        </span>
                    </div>
                </div>
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded-full bg-slate-100">
                    <div id="progress-bar" style="width:0%"
                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-primary-500 transition-all duration-500">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-4">
                <button id="start-btn"
                    class="w-full btn btn-primary py-4 text-lg font-bold shadow-lg shadow-primary-200">
                    Lancer la migration
                </button>
                <p id="error-msg" class="hidden text-sm text-red-600 bg-red-50 p-4 rounded-lg border border-red-100">
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    .mig-applying {
        @apply border-primary-200 bg-primary-50 ring-1 ring-primary-100;
    }

    .mig-done {
        @apply border-green-200 bg-green-50;
    }

    .mig-error {
        @apply border-red-200 bg-red-50;
    }
</style>

<script>
    const startBtn = document.getElementById('start-btn');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const percentageText = document.getElementById('percentage');
    const counterText = document.getElementById('counter');
    const errorMsg = document.getElementById('error-msg');

    const migrations = <?= json_encode(array_keys($pending)) ?>;
    const total = migrations.length;
    let current = 0;

    async function processMigration() {
        if (current >= total) {
            progressText.textContent = "Mise à jour terminée !";
            progressText.className = "text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-200";
            startBtn.textContent = "Accéder à l'application";
            startBtn.className = "w-full btn bg-green-600 hover:bg-green-700 text-white py-4 text-lg font-bold shadow-lg shadow-green-200";
            startBtn.disabled = false;
            startBtn.onclick = () => window.location.href = 'index.php';
            return;
        }

        const mIndex = current;
        const element = document.getElementById('mig-' + mIndex);
        const icon = element.querySelector('.status-icon');
        const status = element.querySelector('.status-text');

        element.classList.add('mig-applying', 'scale-[1.02]');
        icon.classList.remove('bg-slate-300');
        icon.classList.add('bg-primary-500', 'animate-pulse');
        status.textContent = "Migration en cours...";
        status.className = "status-text text-[10px] font-bold uppercase text-primary-500";

        try {
            const response = await fetch('index.php?page=upgrade&action=process');
            const result = await response.json();

            if (result.success) {
                element.classList.remove('mig-applying', 'scale-[1.02]');
                element.classList.add('mig-done');
                icon.classList.remove('bg-primary-500', 'animate-pulse');
                icon.classList.add('bg-green-500');
                status.textContent = "Succès";
                status.className = "status-text text-[10px] font-bold uppercase text-green-500";

                current++;
                const percent = Math.round((current / total) * 100);
                progressBar.style.width = percent + '%';
                percentageText.textContent = percent + '%';
                counterText.textContent = current + ' / ' + total;
                progressText.textContent = "Migration " + current + " / " + total;

                // Wait a bit to show progress smoothly
                setTimeout(processMigration, 300);
            } else {
                throw new Error(result.error);
            }
        } catch (err) {
            element.classList.remove('mig-applying');
            element.classList.add('mig-error');
            icon.classList.remove('bg-primary-500', 'animate-pulse');
            icon.classList.add('bg-red-500');
            status.textContent = "Erreur";
            status.className = "status-text text-[10px] font-bold uppercase text-red-500";

            errorMsg.textContent = "Erreur lors de la mise à jour : " + err.message;
            errorMsg.classList.remove('hidden');
            startBtn.disabled = false;
            startBtn.textContent = "Réessayer";
        }
    }

    startBtn.addEventListener('click', () => {
        startBtn.disabled = true;
        errorMsg.classList.add('hidden');
        progressText.textContent = "Initialisation...";
        processMigration();
    });
</script>