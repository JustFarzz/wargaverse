// File upload handling
document.getElementById("attachment").addEventListener("change", function (e) {
    const file = e.target.files[0];
    const uploadText = document.querySelector(".file-upload-text");

    if (file) {
        // Check file size (5MB limit)
        if (file.size > 5 * 1024 * 1024) {
            alert("Ukuran file terlalu besar. Maksimal 5MB.");
            this.value = "";
            return;
        }

        uploadText.innerHTML = `
                <div class="upload-icon">📄</div>
                <p><strong>${file.name}</strong></p>
                <small>Ukuran: ${(file.size / 1024 / 1024).toFixed(
                    2
                )} MB</small>
            `;
    }
});

// Drag and drop for file upload
const fileUploadArea = document.querySelector(".file-upload-area");
const fileInput = document.getElementById("attachment");

fileUploadArea.addEventListener("dragover", function (e) {
    e.preventDefault();
    fileUploadArea.classList.add("dragover");
});

fileUploadArea.addEventListener("dragleave", function (e) {
    e.preventDefault();
    fileUploadArea.classList.remove("dragover");
});

fileUploadArea.addEventListener("drop", function (e) {
    e.preventDefault();
    fileUploadArea.classList.remove("dragover");

    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        fileInput.dispatchEvent(new Event("change"));
    }
});

// Time validation
document.getElementById("start_time").addEventListener("change", function () {
    const startTime = this.value;
    const endTimeInput = document.getElementById("end_time");

    if (startTime && !endTimeInput.value) {
        // Auto-set end time to 2 hours after start time
        const [hours, minutes] = startTime.split(":");
        const endHours = (parseInt(hours) + 2) % 24;
        endTimeInput.value = `${endHours
            .toString()
            .padStart(2, "0")}:${minutes}`;
    }
});

// Preview function
function previewEvent() {
    const formData = new FormData(document.querySelector(".event-form"));
    const previewContent = document.getElementById("previewContent");

    const title = formData.get("title") || "Judul Kegiatan";
    const description = formData.get("description") || "Belum ada deskripsi";
    const category = formData.get("category") || "Tidak dikategorikan";
    const eventDate = formData.get("event_date") || "";
    const startTime = formData.get("start_time") || "";
    const endTime = formData.get("end_time") || "";
    const location = formData.get("location") || "Belum ditentukan";
    const organizer = formData.get("organizer") || "";
    const maxParticipants =
        formData.get("max_participants") || "Tidak terbatas";
    const requirements =
        formData.get("requirements") || "Tidak ada persyaratan khusus";

    // Format category for display
    const categoryDisplay =
        {
            rapat: "Rapat RT",
            gotong_royong: "Gotong Royong",
            keamanan: "Keamanan",
            sosial: "Kegiatan Sosial",
            olahraga: "Olahraga",
            keagamaan: "Kegiatan Keagamaan",
            perayaan: "Perayaan",
            lainnya: "Lainnya",
        }[category] || category;

    previewContent.innerHTML = `
            <div class="preview-event">
                <div class="preview-header">
                    <h2>${title}</h2>
                    <span class="preview-category">${categoryDisplay}</span>
                </div>
                <div class="preview-details">
                    <div class="preview-item">
                        <strong>📅 Tanggal:</strong> ${
                            eventDate
                                ? new Date(eventDate).toLocaleDateString(
                                      "id-ID",
                                      {
                                          weekday: "long",
                                          year: "numeric",
                                          month: "long",
                                          day: "numeric",
                                      }
                                  )
                                : "Belum ditentukan"
                        }
                    </div>
                    <div class="preview-item">
                        <strong>🕐 Waktu:</strong> ${startTime}${
        endTime ? ` - ${endTime}` : ""
    }
                    </div>
                    <div class="preview-item">
                        <strong>📍 Lokasi:</strong> ${location}
                    </div>
                    <div class="preview-item">
                        <strong>👤 Penyelenggara:</strong> ${
                            organizer || "Tidak disebutkan"
                        }
                    </div>
                    <div class="preview-item">
                        <strong>👥 Max Peserta:</strong> ${maxParticipants}
                    </div>
                    <div class="preview-item">
                        <strong>📋 Persyaratan:</strong> ${requirements}
                    </div>
                </div>
                <div class="preview-description">
                    <h4>Deskripsi:</h4>
                    <p>${description}</p>
                </div>
            </div>
        `;

    document.getElementById("previewModal").style.display = "flex";
}

function closePreview() {
    document.getElementById("previewModal").style.display = "none";
}

// Close modal when clicking outside
window.onclick = function (event) {
    const previewModal = document.getElementById("previewModal");
    if (event.target === previewModal) {
        previewModal.style.display = "none";
    }
};

// Form validation
document.querySelector(".event-form").addEventListener("submit", function (e) {
    const startTime = document.getElementById("start_time").value;
    const endTime = document.getElementById("end_time").value;
    const eventDate = document.getElementById("event_date").value;
    const title = document.getElementById("title").value.trim();
    const location = document.getElementById("location").value.trim();

    // Validate required fields
    if (!title) {
        e.preventDefault();
        alert("Nama kegiatan wajib diisi!");
        document.getElementById("title").focus();
        return false;
    }

    if (!eventDate) {
        e.preventDefault();
        alert("Tanggal kegiatan wajib diisi!");
        document.getElementById("event_date").focus();
        return false;
    }

    if (!startTime) {
        e.preventDefault();
        alert("Waktu mulai wajib diisi!");
        document.getElementById("start_time").focus();
        return false;
    }

    if (!location) {
        e.preventDefault();
        alert("Lokasi kegiatan wajib diisi!");
        document.getElementById("location").focus();
        return false;
    }

    // Validate time
    if (startTime && endTime && startTime >= endTime) {
        e.preventDefault();
        alert("Waktu selesai harus lebih dari waktu mulai!");
        document.getElementById("end_time").focus();
        return false;
    }

    // Validate date is not in the past
    const today = new Date();
    const selectedDate = new Date(eventDate);
    today.setHours(0, 0, 0, 0);
    selectedDate.setHours(0, 0, 0, 0);

    if (selectedDate < today) {
        e.preventDefault();
        alert("Tanggal kegiatan tidak boleh di masa lalu!");
        document.getElementById("event_date").focus();
        return false;
    }

    return true;
});

// Add loading state to submit button
document.querySelector(".event-form").addEventListener("submit", function (e) {
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    submitBtn.innerHTML = '<i class="icon">⏳</i> Menyimpan...';
    submitBtn.disabled = true;

    // Re-enable after 5 seconds as fallback
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 5000);
});
