<?php
/** @var array $pillars */
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="index.php?page=maturity&action=index"
            class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Retour au tableau de bord
        </a>
        <h1 class="text-2xl font-bold italic text-slate-800">Nouvelle auto-évaluation de maturité</h1>
        <p class="text-slate-500">Répondez en toute sincérité pour obtenir un profil de maturité fidèle à la réalité.
        </p>
    </div>

    <form action="index.php?page=maturity&action=store" method="POST" class="space-y-8">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <?php foreach ($pillars as $key => $pillar): ?>
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-lg font-bold text-slate-800">
                        <?= $pillar['label'] ?>
                    </h2>
                </div>
                <div class="p-6 space-y-8">
                    <?php foreach ($pillar['questions'] as $qIndex => $q): ?>
                        <div class="space-y-3">
                            <label class="block text-base font-medium text-slate-700">
                                <?= $q ?>
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                <?php
                                $levels = [
                                    1 => 'Inexistant',
                                    2 => 'Informel',
                                    3 => 'Défini',
                                    4 => 'Maîtrisé',
                                    5 => 'Optimisé'
                                ];
                                foreach ($levels as $val => $label): ?>
                                    <label
                                        class="relative flex flex-col items-center p-3 border rounded-lg cursor-pointer hover:bg-slate-50 transition-colors group">
                                        <input type="radio" name="q_<?= $key ?>_<?= $qIndex ?>" value="<?= $val ?>" required
                                            class="sr-only peer">
                                        <div
                                            class="w-8 h-8 rounded-full border-2 border-slate-200 flex items-center justify-center mb-1 group-hover:border-primary-400 peer-checked:border-primary-600 peer-checked:bg-primary-600 peer-checked:text-white transition-all text-sm font-bold">
                                            <?= $val ?>
                                        </div>
                                        <span class="text-[10px] uppercase font-bold text-slate-400 peer-checked:text-primary-600">
                                            <?= $label ?>
                                        </span>
                                        <div
                                            class="absolute inset-0 border-2 border-transparent peer-checked:border-primary-600 rounded-lg pointer-events-none">
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="card p-6">
            <label for="comments" class="form-label">Commentaires ou notes globales</label>
            <textarea name="comments" id="comments" rows="4" class="form-input"
                placeholder="Décrivez les priorités identifiées ou les blocages particuliers..."></textarea>
        </div>

        <div class="flex justify-end gap-4">
            <a href="index.php?page=maturity&action=index" class="btn btn-outline">Annuler</a>
            <button type="submit" class="btn btn-primary px-8">Enregistrer l'évaluation</button>
        </div>
    </form>
</div>