document.addEventListener("DOMContentLoaded", function () {
    fetch("php/fetch_items.php")
        .then((response) => response.json())
        .then((items) => {
            const container = document.getElementById("live-auction-items");
            container.innerHTML = "";

            if (items.length === 0) {
                container.innerHTML = "<p>No items found.</p>";
                return;
            }

            items.forEach((item) => {
                const card = document.createElement("div");
                card.className = "item-card";

                const imgSrc = item.ImagePath && item.ImagePath !== "" ? item.ImagePath : "placeholder.jpg";

                card.innerHTML = `
                    <img src="${imgSrc}" alt="${item.Name}" class="item-image">
                    <h3>${item.Name}</h3>
                    <p>${item.Description}</p>
                    <p><strong>Category:</strong> ${item.CategoryName}</p>
                    <p><strong>Starting at:</strong> $${item.StartingPrice}</p>

                    <form action="php/place_bid.php" method="POST">
                        <input type="hidden" name="itemID" value="${item.ItemID}">
                        <label for="bidAmount">Your Bid ($):</label><br>
                        <input type="number" name="bidAmount" step="0.01" min="${item.StartingPrice}" required>
                        <br><br>
                        <button type="submit">Place Bid</button>
                    </form>
                `;

                container.appendChild(card);
            });
        })
        .catch((error) => {
            console.error("Error loading items:", error);
            document.getElementById("live-auction-items").innerHTML =
                "<p>Failed to load items.</p>";
        });
});
