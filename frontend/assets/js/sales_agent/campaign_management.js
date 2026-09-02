document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("campaignForm");
    const dropzone = document.getElementById("imageDropzone");
    const imageInput = document.getElementById("posterImageInput");
    const imagePreview = document.getElementById("imagePreview");
    const uploadPlaceholder = document.getElementById("uploadPlaceholder");
    const permToggle = document.getElementById("isPermanentToggle");
    const dateRangeContainer = document.getElementById("dateRangeContainer");
    const endDateInput = document.getElementById("endDateInput");

    // Dropzone & Image Preview Event
    dropzone?.addEventListener("click", () => imageInput.click());
    imageInput?.addEventListener("change", function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove("hidden");
                uploadPlaceholder.classList.add("hidden");
            };
            reader.readAsDataURL(file);
        }
    });

    // Permanent Toggle Control
    permToggle?.addEventListener("change", function () {
        if (this.checked) {
            dateRangeContainer.classList.add("hidden");
            endDateInput.removeAttribute("required");
        } else {
            dateRangeContainer.classList.remove("hidden");
        }
    });

    // Form Reset Helper
    function resetFormUI() {
        form.reset();
        imagePreview.classList.add("hidden");
        uploadPlaceholder.classList.remove("hidden");
        dateRangeContainer.classList.remove("hidden");
    }

    // Submit Campaign Form directly from page
    form?.addEventListener("submit", async function (e) {
        e.preventDefault();
        const submitBtn = document.getElementById("submitBtn");
        submitBtn.disabled = true;
        submitBtn.innerHTML = `Publishing...`;

        const formData = new FormData(form);
        formData.set("is_permanent", permToggle.checked ? "true" : "false");

        try {
            const response = await fetch("http://127.0.0.1:8000/api/v1/campaigns/create", {
                method: "POST",
                body: formData
            });

            const result = await response.json();
            if (response.ok) {
                resetFormUI();
                loadCampaigns();
            } else {
                alert("Error: " + (result.detail || "Failed to publish poster"));
            }
        } catch (error) {
            console.error("Upload error:", error);
            alert("Network error occurred.");
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Publish to Client Dashboard
            `;
        }
    });

    // Fetch and Display Active Cards Feed
    async function loadCampaigns() {
        const grid = document.getElementById("campaignGrid");
        const countBadge = document.getElementById("campaignCountBadge");

        try {
            const response = await fetch("http://127.0.0.1:8000/api/v1/campaigns/active-posts");
            const campaigns = await response.json();

            if (!campaigns || campaigns.length === 0) {
                if (countBadge) countBadge.textContent = "0 Active";
                grid.innerHTML = `
                    <div class="col-span-full py-12 text-center text-slate-400 bg-white rounded-[2rem] border border-slate-100">
                        No active campaigns yet.
                    </div>`;
                return;
            }

            if (countBadge) countBadge.textContent = `${campaigns.length} Active`;

            grid.innerHTML = campaigns.map(item => {
                const badge = item.is_permanent 
                    ? `<span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Permanent</span>`
                    : `<span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100">Limited-Time</span>`;

                const expiryText = item.is_permanent 
                    ? "Active Indefinitely" 
                    : `Expires: ${new Date(item.end_date).toLocaleDateString()} ${new Date(item.end_date).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;

                return `
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="relative h-40 w-full bg-slate-100">
                                <img src="${item.image_url}" class="w-full h-full object-cover" alt="${item.title}" />
                                <div class="absolute top-3 right-3">
                                    ${badge}
                                </div>
                            </div>
                            <div class="p-4">
                                <h4 class="font-bold text-slate-800 text-xs tracking-tight mb-1">${item.title}</h4>
                                <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">${item.description || "No description provided."}</p>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400 font-medium">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                ${expiryText}
                            </span>
                        </div>
                    </div>
                `;
            }).join("");

        } catch (error) {
            console.error("Error loading campaigns:", error);
            grid.innerHTML = `<div class="col-span-full py-8 text-center text-red-500 text-xs">Failed to load campaign feed.</div>`;
        }
    }

    loadCampaigns();
});