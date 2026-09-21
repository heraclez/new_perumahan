<?= $this->extend('layouts/frontend') ?>

<?= $this->section('styles') ?>
<style>
    .siteplan-viewport {
        position: relative;
        width: 100%;
        background: #0f172a;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.18);
        border: 2px solid rgba(255,255,255,0.08);
    }

    .siteplan-panzoom-container {
        position: relative;
        width: 100%;
        display: inline-block;
        transform-origin: 0 0;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: grab;
    }

    .siteplan-panzoom-container:active {
        cursor: grabbing;
    }

    .siteplan-master-img {
        width: 100%;
        height: auto;
        display: block;
        pointer-events: none;
    }

    #fePinsWrapper {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    /* Frontend Interactive Pin Marker */
    .fe-siteplan-pin {
        position: absolute;
        transform: translate(-50%, -100%);
        z-index: 10;
        cursor: pointer;
        pointer-events: auto;
        transition: all 0.25s ease;
    }

    .fe-siteplan-pin:hover,
    .fe-siteplan-pin.active {
        transform: translate(-50%, -100%) scale(1.25);
        z-index: 50;
    }

    .fe-pin-badge {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(0,0,0,0.4);
        border: 2px solid #ffffff;
        white-space: nowrap;
        backdrop-filter: blur(4px);
    }

    .fe-pin-tersedia {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .fe-pin-booking {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .fe-pin-terjual {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .fe-pin-promo-glow {
        border-color: #fef08a !important;
        animation: promoGlow 1.5s infinite alternate;
    }

    @keyframes promoGlow {
        from { box-shadow: 0 0 10px #f59e0b; }
        to { box-shadow: 0 0 22px #ef4444; }
    }

    /* Interactive Floating Popover Card */
    .siteplan-floating-popover {
        position: absolute;
        z-index: 100;
        width: 320px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.25);
        border: 1px solid rgba(0,0,0,0.08);
        display: none;
        overflow: hidden;
        pointer-events: auto;
        animation: popoverFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes popoverFadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .map-control-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.95);
        color: #1e293b;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.2s ease;
    }

    .map-control-btn:hover {
        background: #ffffff;
        color: var(--bs-primary);
        transform: scale(1.08);
    }

    .kavling-table-row {
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .kavling-table-row:hover {
        background-color: #f8fafc !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header Hero -->
<div class="bg-primary text-white py-5">
    <div class="container py-3">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-white-20 text-white rounded-pill px-3 py-1 mb-2 fw-semibold">
                    <i class="bi bi-map-fill me-1"></i> Interactive Master Plan
                </span>
                <h1 class="fw-bold mb-2 text-white">Master Siteplan & Peta Kavling Interaktif</h1>
                <p class="lead text-white-50 mb-0">
                    Jelajahi tata letak klaster, temukan posisi kavling idaman Anda (hook, hadap taman, jalan utama), dan cek ketersediaan unit secara real-time.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <div class="d-inline-flex flex-wrap gap-2 justify-content-lg-end">
                    <span class="badge bg-success rounded-pill px-3 py-2 fs-7 shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i> <?= esc($counts['tersedia']) ?> Unit Tersedia
                    </span>
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fs-7 shadow-sm">
                        <i class="bi bi-clock-history me-1"></i> <?= esc($counts['booking']) ?> Booking
                    </span>
                    <span class="badge bg-danger rounded-pill px-3 py-2 fs-7 shadow-sm">
                        <i class="bi bi-x-circle-fill me-1"></i> <?= esc($counts['terjual']) ?> Terjual
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <?php if (!$siteplan): ?>
        <div class="text-center py-5">
            <div class="card card-custom border-0 p-5 bg-white shadow-sm rounded-4">
                <i class="bi bi-map fs-1 text-muted d-block mb-3"></i>
                <h4 class="fw-bold text-dark">Master Siteplan Sedang Dipersiapkan</h4>
                <p class="text-muted small mb-3">Tim kami sedang memperbarui denah kavling resmi. Anda dapat melihat katalog tipe rumah di bawah ini:</p>
                <a href="<?= site_url('properti') ?>" class="btn btn-primary rounded-pill px-4">Lihat Katalog Tipe Rumah</a>
            </div>
        </div>
    <?php else: ?>

        <!-- Phase / Area Siteplan Switcher (If multiple active siteplans exist) -->
        <?php if (!empty($activeSiteplans) && count($activeSiteplans) > 1): ?>
            <div class="card card-custom border-0 p-3 mb-4 bg-white shadow-sm rounded-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="badge-primary-soft p-2 rounded-3 text-primary d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i class="bi bi-layers-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Pilih Denah Area / Tahap Siteplan</h6>
                            <small class="text-muted">Tersedia <?= count($activeSiteplans) ?> denah klaster aktif</small>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($activeSiteplans as $spTab): ?>
                            <?php $isCurrentTab = ($siteplan && $siteplan['id'] == $spTab['id']); ?>
                            <a href="<?= site_url('siteplan/' . $spTab['id']) ?>" 
                               class="btn rounded-pill px-3 py-2 d-flex align-items-center gap-2 <?= $isCurrentTab ? 'btn-primary shadow-sm fw-bold' : 'btn-outline-secondary bg-light text-dark' ?>">
                                <i class="bi bi-geo-alt-fill <?= $isCurrentTab ? 'text-white' : 'text-primary' ?>"></i>
                                <span><?= esc($spTab['title']) ?></span>
                                <?php if ($isCurrentTab): ?>
                                    <span class="badge bg-white text-primary rounded-pill ms-1" style="font-size: 0.7rem;">Dipilih</span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Interactive Toolbar & Filter Card -->
        <div class="card card-custom border-0 p-4 mb-4 bg-white shadow-sm rounded-4">
            <div class="row g-3 align-items-center">
                <!-- Status Filter -->
                <div class="col-md-4 col-lg-3">
                    <label class="form-label fw-bold small text-muted mb-1">Filter Status Kavling</label>
                    <select id="feFilterStatus" class="form-select bg-light">
                        <option value="">Semua Status Kavling (<?= count($pins) ?>)</option>
                        <option value="Tersedia">🟢 Hanya Tersedia (<?= esc($counts['tersedia']) ?>)</option>
                        <option value="Booking">🟡 Booking (<?= esc($counts['booking']) ?>)</option>
                        <option value="Terjual">🔴 Terjual (<?= esc($counts['terjual']) ?>)</option>
                        <option value="Promo">🔥 Khusus Unit Promo</option>
                    </select>
                </div>

                <!-- Property Type Filter -->
                <div class="col-md-4 col-lg-4">
                    <label class="form-label fw-bold small text-muted mb-1">Filter Tipe Rumah</label>
                    <select id="feFilterProperty" class="form-select bg-light">
                        <option value="">Semua Tipe Rumah</option>
                        <?php foreach ($properties as $prop): ?>
                            <option value="<?= esc($prop['id']) ?>">
                                <?= esc($prop['title']) ?> (Rp <?= number_format($prop['harga'], 0, ',', '.') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Search Kavling Number -->
                <div class="col-md-4 col-lg-3">
                    <label class="form-label fw-bold small text-muted mb-1">Cari Blok / Nomor</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="feSearchKavling" class="form-control bg-light border-start-0" placeholder="Contoh: A1, B2...">
                    </div>
                </div>

                <!-- Reset Filter -->
                <div class="col-12 col-lg-2 d-flex justify-content-lg-end mt-lg-4">
                    <button type="button" class="btn btn-outline-secondary w-100 rounded-pill py-2" id="btnResetFeFilters">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Master Interactive Canvas Viewport -->
        <div class="siteplan-viewport mb-5" id="siteplanViewport">
            <!-- Map Controls (Top Right Overlay) -->
            <div class="position-absolute top-0 end-0 m-3 z-3 d-flex flex-column gap-2">
                <button type="button" class="map-control-btn" id="btnZoomIn" title="Perbesar (Zoom In)">
                    <i class="bi bi-plus-lg fs-5"></i>
                </button>
                <button type="button" class="map-control-btn" id="btnZoomOut" title="Perkecil (Zoom Out)">
                    <i class="bi bi-dash-lg fs-5"></i>
                </button>
                <button type="button" class="map-control-btn" id="btnResetZoom" title="Reset Tampilan Peta">
                    <i class="bi bi-arrows-fullscreen"></i>
                </button>
            </div>

            <!-- Floating Legend (Bottom Left Overlay) -->
            <div class="position-absolute bottom-0 start-0 m-3 z-3 bg-dark bg-opacity-75 text-white p-2 px-3 rounded-pill d-flex align-items-center gap-3 small backdrop-blur shadow-sm">
                <span class="d-flex align-items-center gap-1.5"><span class="badge bg-success rounded-circle p-1"> </span> Tersedia</span>
                <span class="d-flex align-items-center gap-1.5"><span class="badge bg-warning rounded-circle p-1"> </span> Booking</span>
                <span class="d-flex align-items-center gap-1.5"><span class="badge bg-danger rounded-circle p-1"> </span> Terjual</span>
            </div>

            <!-- Interactive Panzoom Map Container -->
            <div class="siteplan-panzoom-container" id="panzoomContainer">
                <?php 
                $imgSrc = str_starts_with($siteplan['image'], 'http') ? $siteplan['image'] : base_url('uploads/siteplan/' . $siteplan['image']);
                ?>
                <img src="<?= esc($imgSrc) ?>" alt="<?= esc($siteplan['title']) ?>" class="siteplan-master-img" id="feSiteplanImg">
                
                <!-- Plotted Interactive Pins -->
                <div id="fePinsWrapper">
                    <?php foreach ($pins as $idx => $pin): ?>
                        <?php 
                        $statusClass = 'fe-pin-' . strtolower($pin['status']);
                        $isPromo = !empty($pin['is_promo']);
                        $promoGlow = $isPromo ? 'fe-pin-promo-glow' : '';
                        $propThumb = $pin['primary_image'] ?? '';
                        if (empty($propThumb)) {
                            $propThumb = 'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=400&auto=format&fit=crop&q=80';
                        } elseif (!str_starts_with($propThumb, 'http')) {
                            $propThumb = base_url('uploads/properties/' . $propThumb);
                        }
                        ?>
                        <div class="fe-siteplan-pin" 
                             id="pin_<?= esc($pin['id']) ?>"
                             data-id="<?= esc($pin['id']) ?>"
                             data-kavling="<?= esc($pin['kavling_number']) ?>"
                             data-status="<?= esc($pin['status']) ?>"
                             data-property-id="<?= esc($pin['property_id']) ?>"
                             data-property-title="<?= esc($pin['property_title']) ?>"
                             data-property-slug="<?= esc($pin['property_slug']) ?>"
                             data-property-price="Rp <?= number_format((float) ($pin['property_harga'] ?? 0), 0, ',', '.') ?>"
                             data-property-lt="<?= esc($pin['luas_tanah']) ?>"
                             data-property-lb="<?= esc($pin['luas_bangunan']) ?>"
                             data-property-spek="<?= esc($pin['spesifikasi_kamar'] ?: '2 KT / 1 KM') ?>"
                             data-is-promo="<?= $isPromo ? '1' : '0' ?>"
                             data-promo-title="<?= esc($pin['promo_title'] ?: '') ?>"
                             data-promo-desc="<?= esc($pin['promo_desc'] ?: '') ?>"
                             data-notes="<?= esc($pin['notes'] ?: '') ?>"
                             data-img="<?= esc($propThumb) ?>"
                             style="left: <?= esc($pin['pos_x']) ?>%; top: <?= esc($pin['pos_y']) ?>%;">
                            
                            <div class="fe-pin-badge <?= $statusClass ?> <?= $promoGlow ?>">
                                <?php if ($isPromo): ?>
                                    <i class="bi bi-fire text-warning"></i>
                                <?php else: ?>
                                    <i class="bi bi-geo-alt-fill"></i>
                                <?php endif; ?>
                                <span><?= esc($pin['kavling_number']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Floating Interactive Popover Detail Card -->
            <div class="siteplan-floating-popover" id="fePinPopover">
                <div class="position-relative">
                    <img src="" id="popoverImg" alt="Kavling" class="w-100 object-fit-cover" style="height: 140px;">
                    <span class="position-absolute top-0 start-0 m-2 badge rounded-pill px-2.5 py-1" id="popoverStatusBadge"></span>
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2 bg-dark bg-opacity-50 p-1.5 rounded-circle shadow-sm" id="btnClosePopover" aria-label="Close"></button>
                </div>
                <div class="p-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <strong class="text-primary fs-6" id="popoverKavlingNumber"></strong>
                        <span class="fw-extrabold text-dark" id="popoverPrice"></span>
                    </div>
                    <h6 class="fw-bold text-dark mb-1 text-truncate" id="popoverTitle"></h6>

                    <!-- Promo badge if active -->
                    <div id="popoverPromoContainer" class="mb-2" style="display: none;">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill small">
                            <i class="bi bi-fire me-1"></i> <span id="popoverPromoTitle"></span>
                        </span>
                    </div>

                    <!-- Specs specs -->
                    <div class="bg-light p-2 rounded-3 mb-2 small text-muted d-flex justify-content-between text-center" style="font-size: 0.72rem;">
                        <div>LT: <strong class="text-dark" id="popoverLT"></strong> m²</div>
                        <div class="border-start"></div>
                        <div>LB: <strong class="text-dark" id="popoverLB"></strong> m²</div>
                        <div class="border-start"></div>
                        <div id="popoverSpek"></div>
                    </div>

                    <p class="text-muted small mb-3 fst-italic" id="popoverNotes" style="font-size: 0.72rem; display: none;"></p>

                    <!-- Action CTA Buttons -->
                    <div class="d-grid gap-1">
                        <a href="#" target="_blank" class="btn btn-success btn-sm rounded-pill fw-bold py-1.5" id="btnPopoverWa">
                            <i class="bi bi-whatsapp me-1"></i> Tanya Kavling Ini via WhatsApp
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm rounded-pill py-1" id="btnPopoverDetail">
                            Lihat Detail & Denah Tipe Ini
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kavling List Table Overview -->
        <div class="card card-custom border-0 p-4 bg-white shadow-sm rounded-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>Daftar Seluruh Kavling Terdaftar</h5>
                <span class="text-muted small">Klik pada baris kavling untuk menyorot posisi di peta denah</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="kavlingDataTable">
                    <thead class="table-light">
                        <tr>
                            <th>Nomor / Blok</th>
                            <th>Tipe Rumah</th>
                            <th>Harga (Rp)</th>
                            <th>Luas Tanah / Bangunan</th>
                            <th>Status</th>
                            <th>Keunggulan Kavling</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pins as $p): ?>
                            <tr class="kavling-table-row" data-pin-id="<?= esc($p['id']) ?>">
                                <td>
                                    <strong class="text-primary"><?= esc($p['kavling_number']) ?></strong>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($p['property_title'] ?: '-') ?></div>
                                    <?php if (!empty($p['is_promo'])): ?>
                                        <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.65rem;">
                                            <i class="bi bi-fire text-danger"></i> <?= esc($p['promo_title'] ?: 'Promo') ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-dark">
                                    Rp <?= number_format((float) ($p['property_harga'] ?? 0), 0, ',', '.') ?>
                                </td>
                                <td class="small text-muted">
                                    LT <?= esc($p['luas_tanah']) ?> m² / LB <?= esc($p['luas_bangunan']) ?> m²
                                </td>
                                <td>
                                    <span class="badge <?= $p['status'] === 'Tersedia' ? 'bg-success' : ($p['status'] === 'Booking' ? 'bg-warning text-dark' : 'bg-danger') ?> rounded-pill px-3 py-1.5">
                                        <?= esc($p['status']) ?>
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <?= esc($p['notes'] ?: '-') ?>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 btn-focus-pin" data-pin-id="<?= esc($p['id']) ?>">
                                        <i class="bi bi-geo-alt-fill me-1"></i> Sorot di Peta
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    var compName = "<?= esc($settings['company_name'] ?? 'Grand Harmoni Residence') ?>";
    var compWa = "<?= esc($settings['company_whatsapp'] ?? '6281234567890') ?>";

    var currentZoom = 1;
    var maxZoom = 2.5;
    var minZoom = 0.8;
    var activePinEl = null;

    // Zoom Controls
    function applyZoom(scale) {
        currentZoom = Math.max(minZoom, Math.min(maxZoom, scale));
        $('#panzoomContainer').css('transform', 'scale(' + currentZoom + ')');
    }

    $('#btnZoomIn').on('click', function() {
        applyZoom(currentZoom + 0.25);
    });

    $('#btnZoomOut').on('click', function() {
        applyZoom(currentZoom - 0.25);
    });

    $('#btnResetZoom').on('click', function() {
        applyZoom(1);
        hidePopover();
    });

    function hidePopover() {
        $('#fePinPopover').fadeOut(150);
        if (activePinEl) {
            activePinEl.removeClass('active');
            activePinEl = null;
        }
    }

    $('#btnClosePopover').on('click', function(e) {
        e.stopPropagation();
        hidePopover();
    });

    // Pin Click & Hover Handling
    function showPinPopover(pinEl) {
        activePinEl = pinEl;
        $('.fe-siteplan-pin').removeClass('active');
        pinEl.addClass('active');

        var kavling = pinEl.data('kavling');
        var status = pinEl.data('status');
        var propTitle = pinEl.data('property-title');
        var propSlug = pinEl.data('property-slug');
        var propPrice = pinEl.data('property-price');
        var propLt = pinEl.data('property-lt');
        var propLb = pinEl.data('property-lb');
        var propSpek = pinEl.data('property-spek');
        var isPromo = pinEl.data('is-promo') == '1';
        var promoTitle = pinEl.data('promo-title');
        var notes = pinEl.data('notes');
        var img = pinEl.data('img');

        $('#popoverKavlingNumber').text(kavling);
        $('#popoverTitle').text(propTitle);
        $('#popoverPrice').text(propPrice);
        $('#popoverLT').text(propLt);
        $('#popoverLB').text(propLb);
        $('#popoverSpek').text(propSpek);
        $('#popoverImg').attr('src', img);

        // Status Badge
        var statusBadge = $('#popoverStatusBadge');
        statusBadge.removeClass('bg-success bg-warning text-dark bg-danger');
        if (status === 'Tersedia') {
            statusBadge.addClass('bg-success text-white').html('<i class="bi bi-check-circle-fill me-1"></i> Tersedia');
        } else if (status === 'Booking') {
            statusBadge.addClass('bg-warning text-dark').html('<i class="bi bi-clock-history me-1"></i> Booking');
        } else {
            statusBadge.addClass('bg-danger text-white').html('<i class="bi bi-x-circle-fill me-1"></i> Terjual');
        }

        // Promo
        if (isPromo && promoTitle) {
            $('#popoverPromoTitle').text(promoTitle);
            $('#popoverPromoContainer').show();
        } else {
            $('#popoverPromoContainer').hide();
        }

        // Notes
        if (notes) {
            $('#popoverNotes').text('Catatan: ' + notes).show();
        } else {
            $('#popoverNotes').hide();
        }

        // Links
        $('#btnPopoverDetail').attr('href', "<?= site_url('properti') ?>/" + propSlug);

        var waMessage = 'Halo ' + compName + ', saya tertarik dengan posisi kavling ' + kavling + ' (' + propTitle + '). Apakah unit kavling ini masih tersedia dan bisa survey lokasi?';
        $('#btnPopoverWa').attr('href', 'https://wa.me/' + compWa + '?text=' + encodeURIComponent(waMessage));

        // Position Popover nicely within viewport
        var pinPos = pinEl.position();
        var viewportW = $('#siteplanViewport').width();
        var viewportH = $('#siteplanViewport').height();
        var popoverW = 320;
        var popoverH = 340;

        var popLeft = pinPos.left * currentZoom + 15;
        var popTop = pinPos.top * currentZoom - 100;

        // Prevent overflow right
        if (popLeft + popoverW > viewportW - 20) {
            popLeft = pinPos.left * currentZoom - popoverW - 15;
        }
        if (popLeft < 10) popLeft = 10;

        // Prevent overflow top/bottom
        if (popTop < 10) popTop = 10;
        if (popTop + popoverH > viewportH - 20) {
            popTop = viewportH - popoverH - 20;
        }

        $('#fePinPopover').css({
            left: popLeft + 'px',
            top: popTop + 'px'
        }).fadeIn(200);
    }

    $(document).on('click', '.fe-siteplan-pin', function(e) {
        e.stopPropagation();
        showPinPopover($(this));
    });

    // Close popover when clicking anywhere outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#fePinPopover').length && !$(e.target).closest('.fe-siteplan-pin').length) {
            hidePopover();
        }
    });

    // Filters Implementation
    function applyFrontendFilters() {
        var statusFilter = $('#feFilterStatus').val();
        var propertyFilter = $('#feFilterProperty').val();
        var searchKavling = $('#feSearchKavling').val().trim().toLowerCase();

        $('.fe-siteplan-pin').each(function() {
            var pin = $(this);
            var pStatus = pin.data('status');
            var pPropId = String(pin.data('property-id'));
            var pKavling = String(pin.data('kavling')).toLowerCase();
            var pIsPromo = pin.data('is-promo') == '1';

            var matchStatus = true;
            if (statusFilter === 'Promo') {
                matchStatus = pIsPromo;
            } else if (statusFilter !== '') {
                matchStatus = (pStatus === statusFilter);
            }

            var matchProperty = (propertyFilter === '' || pPropId === propertyFilter);
            var matchSearch = (searchKavling === '' || pKavling.indexOf(searchKavling) > -1);

            if (matchStatus && matchProperty && matchSearch) {
                pin.show();
            } else {
                pin.hide();
            }
        });

        // Filter Table Rows as well
        $('#kavlingDataTable tbody tr').each(function() {
            var row = $(this);
            var pinId = row.data('pin-id');
            var correspondingPin = $('#pin_' + pinId);
            if (correspondingPin.is(':visible')) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    $('#feFilterStatus, #feFilterProperty').on('change', applyFrontendFilters);
    $('#feSearchKavling').on('input', applyFrontendFilters);

    $('#btnResetFeFilters').on('click', function() {
        $('#feFilterStatus').val('');
        $('#feFilterProperty').val('');
        $('#feSearchKavling').val('');
        applyFrontendFilters();
        applyZoom(1);
    });

    // Focus Pin from Table
    $(document).on('click', '.btn-focus-pin, .kavling-table-row', function(e) {
        var pinId = $(this).data('pin-id');
        var pinEl = $('#pin_' + pinId);
        if (pinEl.length) {
            // Scroll to viewport
            $('html, body').animate({
                scrollTop: $('#siteplanViewport').offset().top - 80
            }, 300);

            showPinPopover(pinEl);
        }
    });
});
</script>
<?= $this->endSection() ?>
