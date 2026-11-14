const searchInput = document.querySelector('input[name="q"]');
const suggestions = document.querySelector("#suggestions");

let searchTimer;

if (searchInput && suggestions) {
  searchInput.addEventListener("input", () => {
    clearTimeout(searchTimer);

    const query = searchInput.value.trim();

    if (query.length < 2) {
      suggestions.replaceChildren();
      return;
    }

    searchTimer = setTimeout(async () => {
      try {
        const response = await fetch(
          `suggestions.php?q=${encodeURIComponent(query)}`
        );
        const results = await response.json();

        suggestions.replaceChildren();

        results.forEach((result) => {
          const item = document.createElement("div");
          item.textContent = `${result.name} (${result.category})`;
          suggestions.append(item);
        });
      } catch (error) {
        suggestions.textContent = "Suggestions unavailable.";
      }
    }, 180);
  });
}