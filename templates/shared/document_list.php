<?php
/**
 * @var string $entityType 'treatment', 'subprocessor', etc.
 * @var int $entityId L'id de l'entité
 * @var App\Entity\Document[] $documents La liste des documents déjà associés
 */
?>
<div class="bg-slate-50 -mx-8 px-8 py-6 border-b border-slate-200">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                </path>
            </svg>
            <h3 class="font-bold text-slate-800 uppercase tracking-wider text-xs">Documents & Preuves (Accountability)
            </h3>
        </div>
        <span class="text-[10px] bg-primary-100 text-primary-700 font-bold px-2 py-0.5 rounded-full uppercase">
            <?= count($documents) ?> document(s)
        </span>
    </div>

    <!-- Liste des documents -->
    <?php if (empty($documents)): ?>
        <p class="text-sm text-slate-500 italic mb-4">Aucune preuve de conformité jointe pour le moment.</p>
    <?php else: ?>
        <div class="space-y-2 mb-6">
            <?php foreach ($documents as $doc): ?>
                <div
                    class="flex items-center justify-between p-3 bg-white border border-slate-200 rounded-lg group hover:border-primary-300 transition-all">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div
                            class="flex-shrink-0 p-2 bg-slate-100 rounded text-slate-500 group-hover:bg-primary-50 group-hover:text-primary-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div class="overflow-hidden">
                            <span class="block text-sm font-medium text-slate-700 truncate"
                                title="<?= htmlspecialchars($doc->fileName) ?>">
                                <?= htmlspecialchars($doc->fileName) ?>
                            </span>
                            <span class="block text-[10px] text-slate-400">
                                <?= round($doc->fileSize / 1024, 1) ?> KB • Ajouté le
                                <?= date('d/m/Y', strtotime($doc->createdAt)) ?>
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <?php
                        $isPrevisualizable = str_contains($doc->fileType, 'image') || str_contains($doc->fileType, 'pdf');
                        ?>
                        <?php if ($isPrevisualizable): ?>
                            <button type="button"
                                onclick="openPreview('<?= $doc->filePath ?>', '<?= addslashes($doc->fileName) ?>', '<?= $doc->fileType ?>')"
                                class="p-1.5 text-slate-400 hover:text-primary-600 transition-colors" title="Prévisualiser">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                            </button>
                        <?php endif; ?>

                        <a href="<?= $doc->filePath ?>" target="_blank"
                            class="p-1.5 text-slate-400 hover:text-primary-600 transition-colors" title="Télécharger">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                        </a>
                        <?php if (($_SESSION['user_role'] ?? '') !== 'guest'): ?>
                            <form action="index.php?page=document&action=delete" method="POST" class="inline"
                                onsubmit="return confirm('Supprimer ce document ?');">
                                <input type="hidden" name="id" value="<?= $doc->id ?>">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 transition-colors"
                                    title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Zone d'upload -->
    <?php if (($_SESSION['user_role'] ?? '') !== 'guest' && $entityId > 0): ?>
        <div class="mt-4">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-tighter mb-2">Ajouter un document (PDF,
                DOCX, JPG...)</label>
            <div class="flex items-center gap-2">
                <input type="file" name="document" class="block w-full text-sm text-slate-500 
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-xs file:font-bold
                    file:bg-primary-50 file:text-primary-700
                    hover:file:bg-primary-100 cursor-pointer
                ">
            </div>
            <p class="text-[10px] text-slate-400 mt-2 italic">DPA, Audits, Preuves techniques de sécurité...</p>
        </div>
    <?php elseif ($entityId <= 0): ?>
        <p class="text-xs text-amber-600 font-medium italic">Veuillez d'abord enregistrer le traitement pour pouvoir y
            joindre des documents.</p>
    <?php endif; ?>
</div>

<!-- Modal de prévisualisation -->
<div id="previewModal" class="fixed inset-0 z-[100] hidden bg-slate-900/90 p-4 md:p-8 backdrop-blur-sm overflow-y-auto">
    <div class="min-h-full flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full flex flex-col modal-animation my-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="p-2 bg-primary-50 text-primary-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                    </div>
                    <h3 id="previewTitle" class="text-lg font-bold text-slate-900 truncate mr-8">Prévisualisation</h3>
                </div>
                <button onclick="closePreview()"
                    class="p-2 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div id="previewContent" class="p-6 bg-slate-50 flex items-center justify-center min-h-[50vh]">
                <!-- Le contenu sera injecté ici par JS -->
            </div>
            <div class="p-4 border-t border-slate-100 flex justify-end bg-white rounded-b-2xl">
                <button onclick="closePreview()" class="btn btn-outline py-2 px-6">Fermer</button>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes modalIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .modal-animation {
        animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    #previewModal.flex-container {
        display: block;
        /* On utilise block pour le wrapper fixed et flex pour l'interne */
    }
</style>

<script>
function openPreview(url, title, type) {
    const modal = document.getElementById('previewModal');
    const content = document.getElementById('previewContent');
    const titleEl = document.getElementById('previewTitle');
    
    // Téléportation : on déplace la modal directement dans le body si ce n'est pas déjà fait
    // Cela évite que les 'transform' des parents ne cassent le 'fixed'
    if (modal.parentNode !== document.body) {
        document.body.appendChild(modal);
    }

    titleEl.innerText = title;
    content.innerHTML = '<div class="flex flex-col items-center gap-3"><div class="animate-spin rounded-full h-10 w-10 border-4 border-primary-500 border-t-transparent"></div><span class="text-slate-500 text-sm font-medium">Chargement...</span></div>';
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Petite temporisation pour laisser l'animation de modal se faire
    setTimeout(() => {
        if (type.includes('image')) {
            content.innerHTML = `<img src="${url}" class="max-w-full h-auto rounded-lg shadow-lg">`;
        } else if (type.includes('pdf')) {
            content.innerHTML = `<iframe src="${url}" class="w-full h-[75vh] rounded-lg border border-slate-200"></iframe>`;
        }
    }, 100);
}

function closePreview() {
    const modal = document.getElementById('previewModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Gestion globale pour fermer la modal (clic extérieur)
document.addEventListener('click', function(e) {
    const modal = document.getElementById('previewModal');
    if (modal && !modal.classList.contains('hidden') && (e.target === modal || e.target.classList.contains('min-h-full'))) {
        closePreview();
    }
});

// Échap pour fermer
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePreview();
});
</script>