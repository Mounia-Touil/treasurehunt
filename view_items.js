document.addEventListener("DOMContentLoaded", function () {
    fetch("fetch_items.php")
        .then((response) => response.json())
        .then((items) => {
            const container = document.getElementById("live-auction-items");
            container.innerHTML = "";

            if (!Array.isArray(items) || items.length === 0) {
                container.innerHTML = "<p>No items found.</p>";
                return;
            }

            items.forEach((item) => {
                const card = document.createElement("div");
                card.className = "item-card";

                const imgSrc = item.ImagePath && item.ImagePath !== "" ? item.ImagePath : "placeholder.jpg";

                card.innerHTML = `
                    <img src="${imgSrc}" alt="${escapeHTML(item.Name)}" class="item-image">
                    <h3>${escapeHTML(item.Name)}</h3>
                    <p>${escapeHTML(item.Description)}</p>
                    <p><strong>Category:</strong> ${escapeHTML(item.CategoryName)}</p>
                    <p><strong>Starting at:</strong> $${parseFloat(item.StartingPrice).toFixed(2)}</p>
                    <p><strong>Ends in:</strong> <span id="timer_${item.ItemID}">Calculating...</span></p>

                    <form action="php/place_bid.php" method="POST">
                        <input type="hidden" name="itemID" value="${item.ItemID}">
                        <label for="bidAmount_${item.ItemID}">Your Bid ($):</label><br>
                        <input type="number" 
                               id="bidAmount_${item.ItemID}" 
                               name="bidAmount" 
                               step="0.01" 
                               min="${item.StartingPrice}" 
                               required><br><br>
                        <button type="submit">Place Bid</button>
                    </form>

                    <button onclick="addToCart(${item.ItemID})">🛒 Add to Cart</button>
                `;

                const form = card.querySelector("form");
                const bidInput = form.querySelector("input[name='bidAmount']");
                form.addEventListener("submit", function (e) {
                    const minBid = parseFloat(bidInput.min);
                    const bidValue = parseFloat(bidInput.value);
                    if (bidValue <= minBid) {
                        e.preventDefault();
                        alert(`Bid needs to be higher than starting price ($${minBid.toFixed(2)}).`);
                    }
                });

                container.appendChild(card);

                if (item.EndTime) {
                    const endTime = new Date(item.EndTime);
                    updateTimer(item.ItemID, endTime);
                    // Store interval ID if you need to clear it later
                    const timerInterval = setInterval(() => updateTimer(item.ItemID, endTime), 1000);
                }
            });
        })
        .catch((error) => {
            console.error("Error loading items:", error);
            document.getElementById("live-auction-items").innerHTML =
                "<p>Failed to load items.</p>";
        });
});

function updateTimer(id, endTime) {
    const now = new Date();
    const distance = endTime - now;
    const timerEl = document.getElementById(`timer_${id}`);

    if (!timerEl) return;  // Safety check

    if (distance <= 0) {
        timerEl.textContent = "Expired";
        return;
    }

    const hours = Math.floor(distance / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    timerEl.textContent = `${hours}h ${minutes}m ${seconds}s`;
}

function escapeHTML(str) {
    if (!str) return "";
    return str.toString().replace(/</g, "&lt;").replace(/>/g, "&gt;");
}

function addToCart(itemID) {
    alert(`🛒 Item ${itemID} added to cart (simulated).`);
}