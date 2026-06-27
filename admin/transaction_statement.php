<?php
include('includes/header.php');

$transactionId = (int) ($_GET['transaction_id'] ?? 0);
$transaction = null;
$detailItems = [];

if ($transactionId > 0) {
    $stmt = $conn->prepare(
        'SELECT h.*, u.firstname, u.middlename, u.lastname, u.account_id, u.account_type, u.currency, u.phone, u.address, u.city, u.state, u.country
         FROM history h
         LEFT JOIN users u ON u.id = h.client_id
         WHERE h.id = ?
         LIMIT 1'
    );
    $stmt->bind_param('i', $transactionId);
    $stmt->execute();
    $transaction = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($transaction) {
    $decodedDetails = json_decode((string) $transaction['details'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedDetails)) {
        $detailItems = $decodedDetails;
    } elseif (trim((string) $transaction['details']) !== '') {
        $detailItems = ['Transaction details' => (string) $transaction['details']];
    }
}

$customerName = '';
if ($transaction) {
    $nameParts = array_filter([
        trim((string) ($transaction['firstname'] ?? '')),
        trim((string) ($transaction['middlename'] ?? '')),
        trim((string) ($transaction['lastname'] ?? '')),
    ]);
    $customerName = $nameParts ? implode(' ', $nameParts) : (string) ($transaction['username'] ?? '');
}

$backLink = 'transfers.php';
if ($transaction && strtolower((string) $transaction['type']) === 'credit') {
    $backLink = 'deposits.php';
}

$statusClass = strtolower((string) ($transaction['status'] ?? 'default'));
$statusBadgeClass = in_array($statusClass, ['completed', 'pending', 'failed'], true) ? $statusClass : 'default';
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Transaction Statement Builder <small>Professional PDF export</small></h1>
        <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?= htmlspecialchars($backLink, ENT_QUOTES, 'UTF-8') ?>">Transactions</a></li>
            <li class="active">Statement Builder</li>
        </ol>
    </section>

    <section class="content">
        <style>
            .statement-builder-shell {
                color: #16324f;
            }

            .statement-hero {
                background: linear-gradient(135deg, #0b3a75 0%, #14519a 100%);
                color: #fff;
                border-radius: 18px;
                padding: 28px 30px;
                margin-bottom: 22px;
                box-shadow: 0 18px 36px rgba(11, 58, 117, 0.18);
            }

            .statement-hero-title {
                font-size: 30px;
                font-weight: 700;
                letter-spacing: 0.4px;
                margin: 0;
            }

            .statement-hero-subtitle {
                font-size: 13px;
                opacity: 0.9;
                margin-top: 6px;
                margin-bottom: 0;
            }

            .statement-hero-meta {
                text-align: right;
                padding-top: 6px;
            }

            .statement-chip {
                display: inline-block;
                padding: 6px 12px;
                border-radius: 999px;
                background: rgba(255,255,255,0.16);
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.5px;
                text-transform: uppercase;
            }

            .statement-meta-line {
                margin-top: 14px;
                font-size: 12px;
                line-height: 1.8;
                opacity: 0.92;
            }

            .statement-summary-card {
                background: #fff;
                border-radius: 14px;
                border: 1px solid #dbe8f5;
                padding: 16px 18px;
                margin-bottom: 18px;
                box-shadow: 0 8px 18px rgba(15, 44, 82, 0.06);
            }

            .statement-summary-label {
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.8px;
                color: #667a91;
                margin-bottom: 8px;
            }

            .statement-summary-value {
                font-size: 21px;
                font-weight: 700;
                color: #0b3a75;
            }

            .statement-panel {
                background: #fff;
                border-radius: 16px;
                border: 1px solid #dbe8f5;
                box-shadow: 0 10px 22px rgba(15, 44, 82, 0.06);
                overflow: hidden;
                margin-bottom: 22px;
            }

            .statement-panel-header {
                padding: 18px 22px 14px;
                border-bottom: 1px solid #e8f0f7;
                background: linear-gradient(180deg, #fbfdff 0%, #f5f9fd 100%);
            }

            .statement-panel-title {
                font-size: 18px;
                font-weight: 700;
                color: #16324f;
                margin: 0;
            }

            .statement-panel-subtitle {
                margin: 6px 0 0;
                color: #6c7d90;
                font-size: 12px;
            }

            .statement-panel-body {
                padding: 20px 22px 22px;
            }

            .statement-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px;
                margin-bottom: 18px;
            }

            .statement-info-item {
                background: #f8fbff;
                border: 1px solid #e2ecf5;
                border-radius: 12px;
                padding: 14px 15px;
                min-height: 88px;
            }

            .statement-info-label {
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.7px;
                color: #6a7d91;
                margin-bottom: 8px;
                font-weight: 700;
            }

            .statement-info-value {
                font-size: 15px;
                font-weight: 700;
                color: #16324f;
                word-break: break-word;
            }

            .statement-info-value.subtle {
                font-size: 13px;
                font-weight: 500;
            }

            .statement-status-pill {
                display: inline-block;
                padding: 5px 11px;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 700;
            }

            .statement-status-pill.completed {
                background: #dcf5e7;
                color: #18794e;
            }

            .statement-status-pill.pending {
                background: #fff3d6;
                color: #9d6800;
            }

            .statement-status-pill.failed {
                background: #fde0df;
                color: #b42318;
            }

            .statement-status-pill.default {
                background: #eaf0f6;
                color: #445568;
            }

            .statement-detail-list {
                border: 1px solid #e7eef5;
                border-radius: 12px;
                overflow: hidden;
            }

            .statement-detail-row {
                display: grid;
                grid-template-columns: 180px 1fr;
                border-bottom: 1px solid #edf2f7;
            }

            .statement-detail-row:last-child {
                border-bottom: 0;
            }

            .statement-detail-key,
            .statement-detail-value {
                padding: 12px 14px;
            }

            .statement-detail-key {
                background: #f8fbff;
                color: #61758a;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .statement-detail-value {
                color: #16324f;
                background: #fff;
            }

            .statement-form-section {
                background: #f8fbff;
                border: 1px solid #e2ecf5;
                border-radius: 14px;
                padding: 16px;
                margin-bottom: 18px;
            }

            .statement-form-section h4 {
                margin: 0 0 12px;
                font-size: 14px;
                font-weight: 700;
                color: #16324f;
            }

            .statement-builder-shell .form-control {
                border-radius: 10px;
                border-color: #cfdceb;
                box-shadow: none;
                min-height: 42px;
            }

            .statement-builder-shell textarea.form-control {
                min-height: 140px;
            }

            .statement-callout {
                border-radius: 14px;
                border: 1px solid #d6e6f7;
                background: linear-gradient(180deg, #f7fbff 0%, #edf5fd 100%);
                padding: 16px 18px;
                color: #25476b;
            }

            .statement-callout h4 {
                margin: 0 0 8px;
                font-weight: 700;
            }

            .statement-callout p {
                margin: 0;
                line-height: 1.65;
            }

            @media (max-width: 991px) {
                .statement-hero-meta {
                    text-align: left;
                    margin-top: 18px;
                }

                .statement-grid {
                    grid-template-columns: 1fr;
                }

                .statement-detail-row {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <?php if (!$transaction): ?>
            <div class="alert alert-danger">
                Transaction not found. <a href="transfers.php">Return to transfers</a> or <a href="deposits.php">view deposits</a>.
            </div>
        <?php else: ?>
            <div class="statement-builder-shell">
                <div class="statement-hero">
                    <div class="row">
                        <div class="col-md-8">
                            <h2 class="statement-hero-title">World Trust Holding</h2>
                            <p class="statement-hero-subtitle">Professional transaction statement preparation for formal customer delivery and internal records.</p>
                        </div>
                        <div class="col-md-4 statement-hero-meta">
                            <span class="statement-chip">Statement Builder</span>
                            <div class="statement-meta-line">
                                Transaction Ref: #<?= htmlspecialchars((string) $transaction['tranx_id'], ENT_QUOTES, 'UTF-8') ?><br>
                                Customer: <?= htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-bottom: 4px;">
                    <div class="col-sm-3 col-xs-6">
                        <div class="statement-summary-card">
                            <div class="statement-summary-label">Amount</div>
                            <div class="statement-summary-value">$<?= number_format((float) $transaction['amount'], 2) ?></div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="statement-summary-card">
                            <div class="statement-summary-label">Type</div>
                            <div class="statement-summary-value" style="font-size:18px;"><?= htmlspecialchars((string) $transaction['type'], ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="statement-summary-card">
                            <div class="statement-summary-label">Status</div>
                            <div style="margin-top:6px;">
                                <span class="statement-status-pill <?= htmlspecialchars($statusBadgeClass, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $transaction['status'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="statement-summary-card">
                            <div class="statement-summary-label">Booked On</div>
                            <div class="statement-summary-value" style="font-size:16px;"><?= date('M d, Y', strtotime((string) $transaction['created_at'])) ?></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                <div class="col-md-5">
                    <div class="statement-panel">
                        <div class="statement-panel-header">
                            <h3 class="statement-panel-title">Transaction Snapshot</h3>
                            <p class="statement-panel-subtitle">Core transaction and account identity shown exactly as the PDF summary will present it.</p>
                        </div>
                        <div class="statement-panel-body">
                            <div class="statement-grid">
                                <div class="statement-info-item">
                                    <div class="statement-info-label">Reference</div>
                                    <div class="statement-info-value">#<?= htmlspecialchars((string) $transaction['tranx_id'], ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                                <div class="statement-info-item">
                                    <div class="statement-info-label">Customer</div>
                                    <div class="statement-info-value"><?= htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                                <div class="statement-info-item">
                                    <div class="statement-info-label">Account Number</div>
                                    <div class="statement-info-value"><?= htmlspecialchars((string) ($transaction['account_id'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                                <div class="statement-info-item">
                                    <div class="statement-info-label">Email</div>
                                    <div class="statement-info-value subtle"><?= htmlspecialchars((string) $transaction['email'], ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                                <div class="statement-info-item">
                                    <div class="statement-info-label">Description</div>
                                    <div class="statement-info-value subtle"><?= htmlspecialchars((string) $transaction['description'], ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                                <div class="statement-info-item">
                                    <div class="statement-info-label">Booked On</div>
                                    <div class="statement-info-value subtle"><?= date('M d, Y h:i A', strtotime((string) $transaction['created_at'])) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="statement-panel">
                        <div class="statement-panel-header">
                            <h3 class="statement-panel-title">Transaction Detail Fields</h3>
                            <p class="statement-panel-subtitle">Structured fields captured with the transaction and inserted into the generated PDF.</p>
                        </div>
                        <div class="statement-panel-body">
                            <?php if ($detailItems): ?>
                                <div class="statement-detail-list">
                                    <?php foreach ($detailItems as $label => $value): ?>
                                        <div class="statement-detail-row">
                                            <div class="statement-detail-key"><?= htmlspecialchars(ucwords(str_replace(['_', '-'], ' ', (string) $label)), ENT_QUOTES, 'UTF-8') ?></div>
                                            <div class="statement-detail-value"><?= nl2br(htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8')) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted" style="margin-bottom:0;">No structured transaction details were found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="statement-panel">
                        <div class="statement-panel-header">
                            <h3 class="statement-panel-title">Statement Settings</h3>
                            <p class="statement-panel-subtitle">Add settlement guidance, administrative notes and the finishing details before generating the final PDF.</p>
                        </div>
                        <form method="post" action="transaction_statement_pdf.php" target="_blank">
                            <div class="statement-panel-body">
                                <input type="hidden" name="transaction_id" value="<?= (int) $transaction['id'] ?>">

                                <div class="statement-form-section">
                                    <h4>Document Identity</h4>
                                    <div class="form-group" style="margin-bottom:0;">
                                        <label for="statement_title">Statement Title</label>
                                        <input type="text" id="statement_title" name="statement_title" class="form-control" value="Official Transaction Statement" maxlength="120">
                                            <p class="help-block" style="margin-bottom:0;">This appears prominently in the PDF header beneath the World Trust Holding brand line.</p>
                                        </div>
                                </div>

                                <div class="statement-form-section">
                                    <h4>Processing Guidance</h4>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="completion_window">Completion Window</label>
                                                <input type="text" id="completion_window" name="completion_window" class="form-control" value="3-5 business days" maxlength="80" placeholder="e.g. 3-5 business days">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                            <label for="prepared_by">Prepared By</label>
                                            <input type="text" id="prepared_by" name="prepared_by" class="form-control" value="Operations Control Desk" maxlength="100">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group" style="margin-bottom:0;">
                                        <label for="status_note">Status Note</label>
                                        <input type="text" id="status_note" name="status_note" class="form-control" value="Transaction is under standard banking review and processing controls." maxlength="180">
                                    </div>
                                </div>

                                <div class="statement-form-section">
                                    <h4>Administrative Notes</h4>
                                    <div class="form-group" style="margin-bottom:0;">
                                        <label for="admin_note">Additional Administrative Notes</label>
                                        <textarea id="admin_note" name="admin_note" class="form-control" rows="6" placeholder="Add any extra professional note to appear in the statement PDF."></textarea>
                                    </div>
                                </div>

                                <div class="statement-callout">
                                    <h4>PDF contents</h4>
                                    <p>The generated PDF will include the customer identity, account information, transaction metadata, full detail breakdown, completion timeline, administrative note, and a centered confidential logo stamp.</p>
                                </div>
                            </div>
                            <div class="box-footer clearfix" style="background:#fff;border-top:1px solid #e8f0f7;padding:18px 22px;">
                                <a href="<?= htmlspecialchars($backLink, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-default">Back</a>
                                <button type="submit" class="btn btn-success pull-right">
                                    <i class="fa fa-file-pdf-o"></i> Generate & Download PDF
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php include('includes/footer.php'); ?>