<?= $this->extend('layouts/frontend') ?>

<?= $this->section('content') ?>

<div class="bg-primary text-white py-5">
    <div class="container py-3">
        <h1 class="fw-bold mb-2 text-white">Kalkulator Simulasi KPR Online</h1>
        <p class="lead text-white-50 mb-0">Hitung estimasi cicilan bulanan, uang muka (DP), dan rasio kelayakan finansial (DSR) rumah idaman Anda.</p>
    </div>
</div>

<div class="container py-5">
    <div class="card card-custom border-0 p-4 p-md-5 bg-white mb-5">
        <div class="row g-4 g-lg-5">
            <!-- Form Inputs -->
            <div class="col-lg-6">
                <h4 class="fw-bold mb-4 text-dark"><i class="bi bi-sliders text-primary me-2"></i>Parameter Simulasi</h4>
                
                <!-- Pilih Properti / Custom -->
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">Pilih Tipe Rumah (Opsional)</label>
                    <select id="selectPropertyPreset" class="form-select form-select-lg">
                        <option value="0" data-price="750000000">-- Masukkan Nominal Bebas --</option>
                        <?php if (!empty($properties)): ?>
                            <?php foreach ($properties as $p): ?>
                                <option value="<?= esc($p['harga']) ?>" data-price="<?= esc($p['harga']) ?>">
                                    <?= esc($p['title']) ?> - Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Harga Properti -->
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">Harga Properti</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light fw-bold">Rp</span>
                        <input type="number" id="kprHarga" class="form-control fw-bold" value="750000000" step="10000000">
                    </div>
                </div>

                <!-- Uang Muka (DP) -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold small text-muted mb-0">Uang Muka (DP)</label>
                        <span class="badge bg-primary px-2 py-1" id="kprDpPercentBadge">10%</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-8">
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" id="kprDpNominal" class="form-control fw-semibold" value="75000000" step="1000000">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <input type="number" id="kprDpPercent" class="form-control fw-semibold" value="10" min="0" max="90">
                                <span class="input-group-text bg-light">%</span>
                            </div>
                        </div>
                    </div>
                    <input type="range" class="form-range mt-2" id="kprDpSlider" min="0" max="90" step="1" value="10">
                </div>

                <!-- Suku Bunga & Tenor -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="form-label fw-bold small text-muted">Suku Bunga KPR / th</label>
                        <div class="input-group">
                            <input type="number" id="kprBunga" class="form-control fw-semibold" value="5.5" min="1" max="25" step="0.1">
                            <span class="input-group-text bg-light">%</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small text-muted">Jangka Waktu (Tenor)</label>
                        <div class="input-group">
                            <input type="number" id="kprTenor" class="form-control fw-semibold" value="20" min="1" max="30" step="1">
                            <span class="input-group-text bg-light">Tahun</span>
                        </div>
                    </div>
                </div>

                <!-- Gaji Bulanan -->
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">Penghasilan Bersih Gabungan Bulanan (Rp)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">Rp</span>
                        <input type="number" id="kprGaji" class="form-control fw-semibold" value="18000000" step="500000">
                    </div>
                    <small class="text-muted" style="font-size: 0.75rem;">* Standar bank: Total cicilan maksimal 30% - 35% dari gaji bersih.</small>
                </div>
            </div>

            <!-- Result Box -->
            <div class="col-lg-6">
                <div class="bg-light p-4 p-md-5 rounded-4 h-100 d-flex flex-column justify-content-between border shadow-sm">
                    <div>
                        <span class="badge bg-primary px-3 py-1 rounded-pill mb-2">HASIL ESTIMASI RESMI</span>
                        <h6 class="text-muted text-uppercase fw-bold letter-spacing-1 mb-1">Estimasi Angsuran Bulanan</h6>
                        <h2 class="display-5 fw-extrabold text-primary my-2" id="kprCicilanOutput">Rp 0</h2>
                        <span class="text-muted d-block mb-4">/ bulan (Suku bunga fix)</span>
                        
                        <div class="bg-white p-4 rounded-3 border mb-4">
                            <div class="d-flex justify-content-between small text-muted mb-2">
                                <span>Plafon Pinjaman Pokok KPR:</span>
                                <strong class="text-dark" id="kprPinjamanOutput">Rp 0</strong>
                            </div>
                            <div class="d-flex justify-content-between small text-muted mb-2">
                                <span>Estimasi Biaya Provisi, Notaris & Bank (5%):</span>
                                <strong class="text-dark" id="kprBiayaNotarisOutput">Rp 0</strong>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between small text-dark fw-bold">
                                <span>Total Pembayaran Pertama (DP + Cicilan 1 + Biaya Notaris):</span>
                                <span class="text-primary fs-6" id="kprTotalAwalOutput">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- DSR Status Alert -->
                    <div id="kprDsrBox" class="alert alert-success border-0 mb-4 p-3 rounded-3">
                        <div class="d-flex gap-2 align-items-center">
                            <i class="bi bi-shield-check fs-3" id="kprDsrIcon"></i>
                            <div>
                                <h6 class="fw-bold mb-0" id="kprDsrTitle">Rasio DSR: Sehat</h6>
                                <p class="mb-0 small" id="kprDsrDesc">Cicilan Anda berada dalam rasio keuangan yang sangat sehat dan disukai perbankan.</p>
                            </div>
                        </div>
                    </div>

                    <a href="https://wa.me/<?= esc($settings['company_whatsapp'] ?? '') ?>?text=Halo%20<?= urlencode($settings['company_name'] ?? '') ?>,%20saya%20sudah%20mencoba%20simulasi%20KPR%20dan%20ingin%20konsultasi%20pengajuan%20bank." target="_blank" class="btn btn-primary btn-lg rounded-pill w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-whatsapp"></i> Ajukan KPR via Konsultan Kami
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        function runCalc() {
            var harga = parseFloat($('#kprHarga').val()) || 0;
            var dpNominal = parseFloat($('#kprDpNominal').val()) || 0;
            var bungaAnnual = parseFloat($('#kprBunga').val()) || 0;
            var tenorYear = parseInt($('#kprTenor').val()) || 0;
            var gaji = parseFloat($('#kprGaji').val()) || 0;

            if (harga <= 0 || tenorYear <= 0) return;

            var pinjaman = Math.max(0, harga - dpNominal);
            var r = (bungaAnnual / 100) / 12;
            var n = tenorYear * 12;

            var cicilan = 0;
            if (r > 0) {
                var factor = Math.pow(1 + r, n);
                cicilan = pinjaman * (r * factor) / (factor - 1);
            } else {
                cicilan = pinjaman / n;
            }

            var biayaBank = harga * 0.05;
            var totalAwal = dpNominal + cicilan + biayaBank;

            function fmt(val) {
                return 'Rp ' + Math.round(val).toLocaleString('id-ID');
            }

            $('#kprCicilanOutput').text(fmt(cicilan));
            $('#kprPinjamanOutput').text(fmt(pinjaman));
            $('#kprBiayaNotarisOutput').text(fmt(biayaBank));
            $('#kprTotalAwalOutput').text(fmt(totalAwal));

            if (gaji > 0) {
                var dsr = (cicilan / gaji) * 100;
                var dsrFixed = dsr.toFixed(1);

                if (dsr <= 30) {
                    $('#kprDsrBox').removeClass('alert-danger alert-warning').addClass('alert-success');
                    $('#kprDsrIcon').removeClass('bi-exclamation-triangle-fill text-danger text-warning').addClass('bi-shield-check text-success');
                    $('#kprDsrTitle').text('Rasio DSR: Sehat (' + dsrFixed + '%)');
                    $('#kprDsrDesc').text('Cicilan berada di bawah batas aman 30% dari penghasilan. Peluang approval bank sangat tinggi!');
                } else if (dsr <= 40) {
                    $('#kprDsrBox').removeClass('alert-success alert-danger').addClass('alert-warning');
                    $('#kprDsrIcon').removeClass('bi-shield-check text-success text-danger').addClass('bi-exclamation-triangle-fill text-warning');
                    $('#kprDsrTitle').text('Rasio DSR: Cukup Beresiko (' + dsrFixed + '%)');
                    $('#kprDsrDesc').text('Cicilan mencapai ' + dsrFixed + '% dari gaji. Disarankan menambah DP atau memperpanjang tenor.');
                } else {
                    $('#kprDsrBox').removeClass('alert-success alert-warning').addClass('alert-danger');
                    $('#kprDsrIcon').removeClass('bi-shield-check text-success text-warning').addClass('bi-exclamation-octagon-fill text-danger');
                    $('#kprDsrTitle').text('Rasio DSR: Beresiko Tinggi (' + dsrFixed + '%)');
                    $('#kprDsrDesc').text('Cicilan melebihi 40% penghasilan. Perlu penyesuaian DP atau pengajuan joint income.');
                }
            }
        }

        $('#selectPropertyPreset').on('change', function() {
            var selectedPrice = parseFloat($(this).find(':selected').data('price')) || 0;
            if (selectedPrice > 0) {
                $('#kprHarga').val(selectedPrice);
                var dpPercent = parseFloat($('#kprDpPercent').val()) || 10;
                $('#kprDpNominal').val(selectedPrice * (dpPercent / 100));
                runCalc();
            }
        });

        $('#kprHarga').on('input', function() {
            var harga = parseFloat($(this).val()) || 0;
            var percent = parseFloat($('#kprDpPercent').val()) || 10;
            $('#kprDpNominal').val(harga * (percent / 100));
            runCalc();
        });

        $('#kprDpNominal').on('input', function() {
            var nominal = parseFloat($(this).val()) || 0;
            var harga = parseFloat($('#kprHarga').val()) || 0;
            if (harga > 0) {
                var percent = Math.min(90, Math.max(0, (nominal / harga) * 100));
                $('#kprDpPercent').val(percent.toFixed(1));
                $('#kprDpSlider').val(percent);
                $('#kprDpPercentBadge').text(percent.toFixed(1) + '%');
            }
            runCalc();
        });

        $('#kprDpPercent').on('input', function() {
            var percent = parseFloat($(this).val()) || 0;
            var harga = parseFloat($('#kprHarga').val()) || 0;
            $('#kprDpNominal').val(harga * (percent / 100));
            $('#kprDpSlider').val(percent);
            $('#kprDpPercentBadge').text(percent + '%');
            runCalc();
        });

        $('#kprDpSlider').on('input', function() {
            var percent = $(this).val();
            var harga = parseFloat($('#kprHarga').val()) || 0;
            $('#kprDpPercent').val(percent);
            $('#kprDpNominal').val(harga * (percent / 100));
            $('#kprDpPercentBadge').text(percent + '%');
            runCalc();
        });

        $('#kprBunga, #kprTenor, #kprGaji').on('input', runCalc);

        runCalc();
    });
</script>
<?= $this->endSection() ?>
