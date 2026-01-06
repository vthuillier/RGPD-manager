<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="index.php?page=dtia&action=list" class="text-sm text-primary-600 hover:text-primary-700 font-bold flex items-center gap-1 mb-2">
            ← Retour à la liste
        </a>
        <h1 class="text-2xl font-bold text-slate-900"><?= $title ?></h1>
        <p class="text-slate-500">Documentez le transfert de données conformément aux recommandations du CEPD.</p>
    </div>

    <form action="index.php?page=dtia&action=store" method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <?php if ($dtia): ?>
            <input type="hidden" name="id" value="<?= $dtia->id ?>">
        <?php endif; ?>

        <div class="card p-6 shadow-sm border-slate-200">
            <h2 class="text-lg font-bold text-slate-800 mb-6 pb-2 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Contexte du transfert
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group lg:col-span-2">
                    <label class="form-label" for="country_name">Pays destinataire</label>
                    <input type="text" id="country_name" name="country_name" class="form-input" 
                           placeholder="Ex: États-Unis, Inde, Japon..." required
                           value="<?= $dtia ? htmlspecialchars($dtia->countryName) : '' ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="transfer_mechanism">Mécanisme de transfert</label>
                    <select id="transfer_mechanism" name="transfer_mechanism" class="form-input" required>
                        <option value="scc" <?= ($dtia && $dtia->transferMechanism === 'scc') ? 'selected' : '' ?>>Clauses Contractuelles Types (SCC)</option>
                        <option value="adequacy" <?= ($dtia && $dtia->transferMechanism === 'adequacy') ? 'selected' : '' ?>>Décision d'adéquation (Art. 45)</option>
                        <option value="bcr" <?= ($dtia && $dtia->transferMechanism === 'bcr') ? 'selected' : '' ?>>Règles d'entreprise (BCR)</option>
                        <option value="derogation" <?= ($dtia && $dtia->transferMechanism === 'derogation') ? 'selected' : '' ?>>Dérogation spécifique (Art. 49)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="assessment_date">Date de l'évaluation</label>
                    <input type="date" id="assessment_date" name="assessment_date" class="form-input" 
                           value="<?= $dtia ? $dtia->assessmentDate : date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="treatment_id">Traitement lié (Registre)</label>
                    <select id="treatment_id" name="treatment_id" class="form-input">
                        <option value="">-- Aucun --</option>
                        <?php foreach ($treatments as $t): ?>
                            <option value="<?= $t->id ?>" <?= ($dtia && $dtia->treatmentId === $t->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="subprocessor_id">Sous-traitant lié</label>
                    <select id="subprocessor_id" name="subprocessor_id" class="form-input">
                        <option value="">-- Aucun --</option>
                        <?php foreach ($subprocessors as $s): ?>
                            <option value="<?= $s->id ?>" <?= ($dtia && $dtia->subprocessorId === $s->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="card p-6 shadow-sm border-slate-200">
            <h2 class="text-lg font-bold text-slate-800 mb-6 pb-2 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Parties et Données
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label class="form-label" for="data_exporter">Exportateur (En Europe)</label>
                    <input type="text" id="data_exporter" name="data_exporter" class="form-input" 
                           placeholder="Nom de votre organisation" required
                           value="<?= $dtia ? htmlspecialchars($dtia->dataExporter) : $_SESSION['organization_name'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="data_importer">Importateur (Hors Europe)</label>
                    <input type="text" id="data_importer" name="data_importer" class="form-input" 
                           placeholder="Ex: AWS Inc, Salesforce, ..." required
                           value="<?= $dtia ? htmlspecialchars($dtia->dataImporter) : '' ?>">
                </div>

                <div class="form-group lg:col-span-2">
                    <label class="form-label" for="data_categories">Catégories de données transférées</label>
                    <textarea id="data_categories" name="data_categories" class="form-input h-24" 
                              placeholder="Détails des données transférées (identifiants, données bancaires, etc.)" required><?= $dtia ? htmlspecialchars($dtia->dataCategories) : '' ?></textarea>
                </div>
            </div>
        </div>

        <div class="card p-6 shadow-sm border-slate-200">
            <h2 class="text-lg font-bold text-slate-800 mb-6 pb-2 border-b border-slate-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Analyse Schrems II & Mesures
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label class="form-label" for="risk_level">Niveau de risque résiduel</label>
                    <select id="risk_level" name="risk_level" class="form-input" required>
                        <option value="low" <?= ($dtia && $dtia->riskLevel === 'low') ? 'selected' : '' ?>>Faible (OK)</option>
                        <option value="medium" <?= ($dtia && $dtia->riskLevel === 'medium') ? 'selected' : '' ?>>Modéré (Mesures requises)</option>
                        <option value="high" <?= ($dtia && $dtia->riskLevel === 'high') ? 'selected' : '' ?>>Élevé (Attention)</option>
                        <option value="critical" <?= ($dtia && $dtia->riskLevel === 'critical') ? 'selected' : '' ?>>Critique (Transfert déconseillé)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Statut</label>
                    <select id="status" name="status" class="form-input">
                        <option value="draft" <?= ($dtia && $dtia->status === 'draft') ? 'selected' : '' ?>>Brouillon</option>
                        <option value="completed" <?= ($dtia && $dtia->status === 'completed') ? 'selected' : '' ?>>Finalisé & Validé</option>
                    </select>
                </div>

                <div class="form-group lg:col-span-2">
                    <label class="form-label" for="supplementary_measures">Mesures supplémentaires (Techniques, Organisationnelles, Juridiques)</label>
                    <textarea id="supplementary_measures" name="supplementary_measures" class="form-input h-32" 
                              placeholder="Ex: Chiffrement de bout en bout où l'importateur n'a pas la clé, Pseudonymisation, etc."><?= $dtia ? htmlspecialchars($dtia->supplementaryMeasures ?? '') : '' ?></textarea>
                    <p class="text-xs text-slate-400 mt-2 italic">Obligatoire si le mécanisme SCC est utilisé et que les lois du pays tiers permettent des accès gouvernementaux excessifs.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="index.php?page=dtia&action=list" class="btn btn-outline">Annuler</a>
            <button type="submit" class="btn btn-primary px-8">Enregistrer la DTIA</button>
        </div>
    </form>
</div>
