<?= $this->extend('layouts/admin') ?>

<?= $this->section('styles') ?>
<style>
    /* Builder Canvas Container: Centered, wraps image tightly with zero gap */
    .canvas-stage-wrapper {
        background-color: #0f172a;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 500px;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.5);
    }

    .siteplan-builder-canvas {
        position: relative;
        display: inline-block;
        max-width: 100%;
        line-height: 0;
        border-radius: 12px;
        overflow: visible;
        user-select: none;
        box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        border: 2px solid rgba(255,255,255,0.15);
    }

    .siteplan-builder-img {
        display: block;
        max-width: 100%;
        height: auto;
        cursor: crosshair;
        border-radius: 10px;
    }

    .pins-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        line-height: normal;
        border-radius: 10px;
    }

    /* Click Ripple Target Animation */
    .canvas-click-ripple {
        position: absolute;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 3px solid #38bdf8;
        background: rgba(56, 189, 248, 0.4);
        transform: translate(-50%, -50%) scale(0.2);
        animation: rippleTarget 0.6s ease-out forwards;
        pointer-events: none;
        z-index: 999;
    }

    @keyframes rippleTarget {
        0% { transform: translate(-50%, -50%) scale(0.2); opacity: 1; }
        100% { transform: translate(-50%, -50%) scale(2.2); opacity: 0; }
    }

    /* High Visibility Interactive Pin */
    .plotter-pin {
        position: absolute;
        transform: translate(-50%, -100%);
        z-index: 100;
        cursor: move;
        pointer-events: auto;
        transition: transform 0.15s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .plotter-pin:hover {
        transform: translate(-50%, -100%) scale(1.2);
        z-index: 200;
    }

    .plotter-pin-bubble {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 800;
        color: #ffffff;
        box-shadow: 0 6px 18px rgba(0,0,0,0.5);
        border: 2px solid #ffffff;
        white-space: nowrap;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4);
    }

    .plotter-pin-tail {
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        margin-top: -1px;
    }

    /* Status Themes */
    .pin-status-tersedia .plotter-pin-bubble {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .pin-status-tersedia .plotter-pin-tail {
        border-top: 8px solid #059669;
    }

    .pin-status-booking .plotter-pin-bubble {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    .pin-status-booking .plotter-pin-tail {
        border-top: 8px solid #d97706;
    }

    .pin-status-terjual .plotter-pin-bubble {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }
    .pin-status-terjual .plotter-pin-tail {
        border-top: 8px solid #dc2626;
    }

    /* Unsaved / Newly Placed Pin Visual Glow */
    .plotter-pin.is-unsaved .plotter-pin-bubble {
        border-color: #fef08a !important;
        box-shadow: 0 0 0 3px #f59e0b, 0 8px 24px rgba(245, 158, 11, 0.8) !important;
        animation: unsavedPinPulse 1.4s infinite alternate;
    }

    @keyframes unsavedPinPulse {
        from { box-shadow: 0 0 0 2px #f59e0b, 0 4px 12px rgba(245, 158, 11, 0.5); }
        to { box-shadow: 0 0 0 6px #fbbf24, 0 8px 28px rgba(251, 191, 36, 0.95); }
    }

    /* Sticky Floating Unsaved Alert Bar */
    .unsaved-floating-bar {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%) translateY(120px);
        z-index: 1050;
        background: #0f172a;
        color: #ffffff;
        padding: 12px 24px;
        border-radius: 50px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        border: 2px solid #f59e0b;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
        opacity: 0;
        pointer-events: none;
    }

    .unsaved-floating-bar.show {
        transform: translateX(-50%) translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    .btn-save-pulse {
        animation: btnPulseGlow 1.5s infinite alternate;
    }

    @keyframes btnPulseGlow {
        from { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
        to { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Top Actions Bar -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="<?= site_url('admin/siteplan') ?>" class="btn btn-outline-secondary btn-sm rounded-circle" title="Kembali ke Daftar">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="fw-bold mb-0 text-dark"><?= esc($siteplan['title']) ?></h4>
            <span class="badge bg-primary rounded-pill px-3 py-1 small" id="badgeTotalPins">
                <?= count($pins) ?> Kavling
            </span>
            <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 small" id="badgeUnsavedIndicator" style="display: none;">
                <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Ada Perubahan Belum Disimpan
            </span>
        </div>
        <p class="text-muted small mb-0">Klik pada gambar denah untuk menandai kavling baru. Drag pin untuk mengatur posisi secara presisi.</p>
    </div>
    
    <div class="d-flex align-items-center gap-2">
        <a href="<?= site_url('siteplan') ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold">
            <i class="bi bi-eye me-1"></i> Preview Publik
        </a>
        <button type="button" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm" id="btnSaveAllPins">
            <i class="bi bi-check-circle-fill me-1"></i> Simpan Semua Posisi PIN
        </button>
    </div>
</div>

<!-- Instructions & Legend Card -->
<div class="card card-table border-0 shadow-sm p-3 mb-4 bg-white">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i class="bi bi-pin-map-fill"></i>
            </div>
            <div class="small">
                <strong class="text-dark">Petunjuk Menandai:</strong> Setiap kali Anda mengklik kavling di denah, pin akan <strong>langsung muncul di titik tersebut</strong>. Klik pin untuk mengedit tipe/status kavling. Setelah selesai, klik tombol <strong>"Simpan Semua Posisi PIN"</strong>.
            </div>
        </div>

        <!-- Status Legend -->
        <div class="d-flex align-items-center gap-3 small fw-bold">
            <span class="d-flex align-items-center gap-1 text-success">
                <span class="badge bg-success rounded-circle p-1"> </span> Tersedia
            </span>
            <span class="d-flex align-items-center gap-1 text-warning">
                <span class="badge bg-warning rounded-circle p-1"> </span> Booking
            </span>
            <span class="d-flex align-items-center gap-1 text-danger">
                <span class="badge bg-danger rounded-circle p-1"> </span> Terjual
            </span>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Left: Interactive Canvas Plotter (Centered with Tight Wrapper) -->
    <div class="col-lg-9">
        <div class="canvas-stage-wrapper">
            <div class="siteplan-builder-canvas" id="canvasWrapper">
                <?php 
                $imgSrc = str_starts_with($siteplan['image'], 'http') ? $siteplan['image'] : base_url('uploads/siteplan/' . $siteplan['image']);
                ?>
                <img src="<?= esc($imgSrc) ?>" alt="Siteplan Master" class="siteplan-builder-img" id="siteplanImage">
                <div class="pins-overlay" id="pinsContainer">
                    <!-- Pins will be rendered dynamically via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Plotted Pins List Drawer -->
    <div class="col-lg-3">
        <div class="card card-table border-0 p-3 shadow-sm h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-list-check text-primary me-1"></i>Daftar Pin Kavling</h6>
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" id="btnClearAllPins" title="Hapus Semua Pin">
                    Reset
                </button>
            </div>
            
            <input type="text" id="searchPinInput" class="form-control form-control-sm rounded-pill mb-3" placeholder="Cari nomor blok/kavling...">

            <div class="overflow-y-auto flex-grow-1 pe-1" id="pinsListDrawer" style="max-height: 520px;">
                <!-- Pin items rendered by JS -->
            </div>
        </div>
    </div>
</div>

<!-- Sticky Floating Unsaved Notification Bar -->
<div class="unsaved-floating-bar" id="unsavedFloatingBar">
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-warning text-dark rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
            <i class="bi bi-exclamation-lg fs-6"></i>
        </span>
        <div>
            <div class="fw-bold text-white small" id="unsavedTextTitle">Terdapat Pin Baru / Perubahan Posisi</div>
            <div class="text-white-50" style="font-size: 0.72rem;">Klik tombol simpan agar tersimpan permanen ke website.</div>
        </div>
    </div>
    <button type="button" class="btn btn-success btn-sm rounded-pill px-4 py-2 fw-bold shadow-sm btn-save-pulse" id="btnFloatingSave">
        <i class="bi bi-check-circle-fill me-1"></i> Simpan Sekarang
    </button>
</div>

<!-- Modal Edit / Add Pin Details -->
<div class="modal fade" id="pinEditModal" tabindex="-1" aria-labelledby="pinEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="formPinEdit">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="pinEditModalLabel">
                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>Pengaturan Pin Kavling
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="modalPinIndex" value="">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nomor / Kode Blok Kavling <span class="text-danger">*</span></label>
                        <input type="text" id="modalKavlingNumber" class="form-control" placeholder="Contoh: Blok A1 - No. 05" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Tipe Properti / Model Rumah <span class="text-danger">*</span></label>
                        <select id="modalPropertyId" class="form-select" required>
                            <option value="">-- Pilih Tipe Rumah --</option>
                            <?php foreach ($properties as $prop): ?>
                                <option value="<?= esc($prop['id']) ?>">
                                    <?= esc($prop['title']) ?> (Rp <?= number_format($prop['harga'], 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Status Kavling <span class="text-danger">*</span></label>
                        <select id="modalStatus" class="form-select" required>
                            <option value="Tersedia">Tersedia (Ready - Hijau)</option>
                            <option value="Booking">Booking (Dalam Proses - Kuning)</option>
                            <option value="Terjual">Terjual (Sold Out - Merah)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Catatan Posisi / Keunggulan Kavling (Opsional)</label>
                        <input type="text" id="modalNotes" class="form-control" placeholder="Contoh: Posisi Hook, Hadap Timur / Taman Tematik">
                    </div>

                    <div class="row g-2 text-muted small bg-light p-2 rounded-3">
                        <div class="col-6">Posisi X: <strong id="modalCoordX">0%</strong></div>
                        <div class="col-6">Posisi Y: <strong id="modalCoordY">0%</strong></div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" id="btnDeleteCurrentPin">
                        <i class="bi bi-trash me-1"></i> Hapus Pin Ini
                    </button>
                    <div>
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="bi bi-check-lg me-1"></i> Terapkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initial Pins data passed from PHP
    var pins = <?= json_encode($pins) ?> || [];
    var properties = <?= json_encode($properties) ?> || [];
    var activeModalIndex = -1;
    var isDragging = false;
    var draggedPinIndex = -1;
    var hasUnsavedChanges = false;

    // Helper map of properties by ID
    var propertyMap = {};
    $.each(properties, function(i, p) {
        propertyMap[p.id] = p;
    });

    function setUnsaved(status) {
        hasUnsavedChanges = status;
        if (status) {
            $('#badgeUnsavedIndicator').fadeIn(200);
            $('#unsavedFloatingBar').addClass('show');
            $('#btnSaveAllPins').addClass('btn-save-pulse');
        } else {
            $('#badgeUnsavedIndicator').fadeOut(200);
            $('#unsavedFloatingBar').removeClass('show');
            $('#btnSaveAllPins').removeClass('btn-save-pulse');
            $.each(pins, function(i, p) {
                p.is_new = false;
            });
        }
    }

    // Warn before leave if unsaved
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'Terdapat perubahan posisi pin kavling yang belum Anda simpan. Yakin ingin keluar?';
        }
    });

    function renderPins() {
        var container = $('#pinsContainer');
        var drawer = $('#pinsListDrawer');
        container.empty();
        drawer.empty();

        $('#badgeTotalPins').text(pins.length + ' Kavling');

        $.each(pins, function(index, pin) {
            var statusLower = pin.status ? pin.status.toLowerCase() : 'tersedia';
            var statusThemeClass = 'pin-status-' + statusLower;
            var propName = propertyMap[pin.property_id] ? propertyMap[pin.property_id].title : 'Tipe Rumah';
            var unsavedClass = (pin.is_new || hasUnsavedChanges) ? 'is-unsaved' : '';
            var unsavedTag = pin.is_new ? '<span class="badge bg-warning text-dark ms-1" style="font-size: 0.6rem;">Baru</span>' : '';

            // 1. Render on Image Canvas Overlay (Absolute % positioning)
            var pinHtml = '<div class="plotter-pin ' + statusThemeClass + ' ' + unsavedClass + '" data-index="' + index + '" style="left: ' + pin.pos_x + '%; top: ' + pin.pos_y + '%;">' +
                '<div class="plotter-pin-bubble">' +
                    '<i class="bi bi-geo-alt-fill"></i> ' +
                    '<span>' + $('<div>').text(pin.kavling_number || 'Kavling').html() + '</span>' +
                '</div>' +
                '<div class="plotter-pin-tail"></div>' +
            '</div>';
            container.append(pinHtml);

            // 2. Render in Sidebar Drawer
            var statusBadge = pin.status === 'Tersedia' ? 'bg-success' : (pin.status === 'Booking' ? 'bg-warning text-dark' : 'bg-danger');
            var drawerHtml = '<div class="card border rounded-3 p-2 mb-2 bg-light pin-drawer-item ' + (pin.is_new ? 'border-warning' : '') + '" data-index="' + index + '">' +
                '<div class="d-flex justify-content-between align-items-center mb-1">' +
                    '<div><strong class="text-dark small">' + $('<div>').text(pin.kavling_number || '-').html() + '</strong>' + unsavedTag + '</div>' +
                    '<span class="badge ' + statusBadge + ' rounded-pill" style="font-size: 0.65rem;">' + pin.status + '</span>' +
                '</div>' +
                '<div class="text-muted" style="font-size: 0.72rem;">' + $('<div>').text(propName).html() + '</div>' +
                '<div class="d-flex justify-content-between align-items-center mt-1 pt-1 border-top" style="font-size: 0.7rem;">' +
                    '<span class="text-muted">X: ' + pin.pos_x + '% | Y: ' + pin.pos_y + '%</span>' +
                    '<button type="button" class="btn btn-link text-primary p-0 text-decoration-none btn-edit-drawer-pin" data-index="' + index + '">Edit</button>' +
                '</div>' +
            '</div>';
            drawer.append(drawerHtml);
        });

        if (pins.length === 0) {
            drawer.html('<div class="text-muted text-center py-4 small">Belum ada pin. Klik denah untuk membuat pin kavling baru.</div>');
        }
    }

    renderPins();

    // Canvas Click directly on Image to add new pin with pixel-perfect getBoundingClientRect
    $('#siteplanImage, #pinsContainer').on('click', function(e) {
        if ($(e.target).closest('.plotter-pin').length > 0) {
            return; // Clicked on existing pin
        }

        var imgEl = document.getElementById('siteplanImage');
        var rect = imgEl.getBoundingClientRect();

        var clickX = e.clientX - rect.left;
        var clickY = e.clientY - rect.top;

        if (clickX < 0 || clickY < 0 || clickX > rect.width || clickY > rect.height) {
            return; // Clicked outside the image boundaries
        }

        var posX = Math.max(0, Math.min(100, Math.round((clickX / rect.width) * 1000) / 10));
        var posY = Math.max(0, Math.min(100, Math.round((clickY / rect.height) * 1000) / 10));

        // Visual click ripple target animation
        var ripple = $('<div class="canvas-click-ripple"></div>').css({
            left: posX + '%',
            top: posY + '%'
        });
        $('#pinsContainer').append(ripple);
        setTimeout(function() {
            ripple.remove();
        }, 600);

        // Auto determine next kavling name
        var nextNum = pins.length + 1;
        var defaultKavling = 'Kavling ' + (nextNum < 10 ? '0' + nextNum : nextNum);
        var defaultPropId = properties.length > 0 ? properties[0].id : '';

        // Add new pin object with is_new flag
        pins.push({
            siteplan_id: <?= (int) $siteplan['id'] ?>,
            property_id: defaultPropId,
            kavling_number: defaultKavling,
            status: 'Tersedia',
            pos_x: posX,
            pos_y: posY,
            notes: '',
            is_new: true
        });

        setUnsaved(true);
        renderPins();

        // Automatically open edit modal for this new pin
        openPinModal(pins.length - 1);
    });

    // Click on pin to open edit modal
    $(document).on('click', '.plotter-pin, .btn-edit-drawer-pin', function(e) {
        e.stopPropagation();
        var index = parseInt($(this).data('index'));
        openPinModal(index);
    });

    function openPinModal(index) {
        activeModalIndex = index;
        var pin = pins[index];
        if (!pin) return;

        $('#modalPinIndex').val(index);
        $('#modalKavlingNumber').val(pin.kavling_number || '');
        $('#modalPropertyId').val(pin.property_id || '');
        $('#modalStatus').val(pin.status || 'Tersedia');
        $('#modalNotes').val(pin.notes || '');
        $('#modalCoordX').text(pin.pos_x + '%');
        $('#modalCoordY').text(pin.pos_y + '%');

        var modalEl = new bootstrap.Modal(document.getElementById('pinEditModal'));
        modalEl.show();
    }

    // Handle Pin Edit Form Submit
    $('#formPinEdit').on('submit', function(e) {
        e.preventDefault();
        if (activeModalIndex >= 0 && activeModalIndex < pins.length) {
            pins[activeModalIndex].kavling_number = $('#modalKavlingNumber').val().trim();
            pins[activeModalIndex].property_id = $('#modalPropertyId').val();
            pins[activeModalIndex].status = $('#modalStatus').val();
            pins[activeModalIndex].notes = $('#modalNotes').val().trim();

            setUnsaved(true);
            renderPins();
            bootstrap.Modal.getInstance(document.getElementById('pinEditModal')).hide();
        }
    });

    // Delete single pin
    $('#btnDeleteCurrentPin').on('click', function() {
        if (activeModalIndex >= 0 && activeModalIndex < pins.length) {
            pins.splice(activeModalIndex, 1);
            setUnsaved(true);
            renderPins();
            bootstrap.Modal.getInstance(document.getElementById('pinEditModal')).hide();
        }
    });

    // Reset all pins
    $('#btnClearAllPins').on('click', function() {
        if (confirm('Hapus semua pin kavling dari canvas ini?')) {
            pins = [];
            setUnsaved(true);
            renderPins();
        }
    });

    // Search Pin drawer filter
    $('#searchPinInput').on('input', function() {
        var query = $(this).val().toLowerCase();
        $('.pin-drawer-item').each(function() {
            var text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(query) > -1);
        });
    });

    // Drag and Drop Pin positioning on Canvas
    $(document).on('mousedown', '.plotter-pin', function(e) {
        isDragging = true;
        draggedPinIndex = parseInt($(this).data('index'));
        var pinEl = $(this);
        var imgEl = document.getElementById('siteplanImage');

        $(document).on('mousemove.pindrag', function(eMove) {
            if (!isDragging) return;
            var rect = imgEl.getBoundingClientRect();
            var currentX = eMove.clientX - rect.left;
            var currentY = eMove.clientY - rect.top;

            var pctX = Math.max(0, Math.min(100, Math.round((currentX / rect.width) * 1000) / 10));
            var pctY = Math.max(0, Math.min(100, Math.round((currentY / rect.height) * 1000) / 10));

            pinEl.css({ left: pctX + '%', top: pctY + '%' });
            if (pins[draggedPinIndex]) {
                pins[draggedPinIndex].pos_x = pctX;
                pins[draggedPinIndex].pos_y = pctY;
            }
        });

        $(document).on('mouseup.pindrag', function() {
            if (isDragging) {
                isDragging = false;
                $(document).off('.pindrag');
                setUnsaved(true);
                renderPins();
            }
        });
    });

    // Save all pins via AJAX to Server
    function executeSave() {
        var btn = $('#btnSaveAllPins');
        var btnFloating = $('#btnFloatingSave');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');
        btnFloating.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: "<?= site_url('admin/siteplan/save-pins/' . $siteplan['id']) ?>",
            type: "POST",
            data: {
                pins: JSON.stringify(pins),
                "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
            },
            dataType: "json",
            success: function(res) {
                btn.prop('disabled', false).html('<i class="bi bi-check-circle-fill me-1"></i> Simpan Semua Posisi PIN');
                btnFloating.prop('disabled', false).html('<i class="bi bi-check-circle-fill me-1"></i> Simpan Sekarang');
                if (res.success) {
                    setUnsaved(false);
                    renderPins();
                    alert(res.message || 'Semua pin kavling berhasil disimpan!');
                } else {
                    alert('Gagal: ' + (res.message || 'Terjadi kesalahan.'));
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="bi bi-check-circle-fill me-1"></i> Simpan Semua Posisi PIN');
                btnFloating.prop('disabled', false).html('<i class="bi bi-check-circle-fill me-1"></i> Simpan Sekarang');
                alert('Gagal menghubungi server. Silakan coba lagi.');
            }
        });
    }

    $('#btnSaveAllPins, #btnFloatingSave').on('click', function() {
        executeSave();
    });
});
</script>
<?= $this->endSection() ?>
