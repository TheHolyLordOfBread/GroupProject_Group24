<nav class="navbar">
    <div class="logo">
        <label>
            select a page:
            <select id="redirectDropdown" onChange="selectRedirect()">
                <option value="http://localhost/GROUPPROJECT_GROUP24/Index.php">Individual Graphs</option>
                <option value="http://localhost/GROUPPROJECT_GROUP24/comparegraphs.php">Rainfall Comparison</option>
                <option value="http://localhost/GROUPPROJECT_GROUP24/nahrim-map.php">Nahrim Map</option>
                <option value="http://localhost/GROUPPROJECT_GROUP24/open_meteo.php">Open Meteo Map</option>
                <option value="http://localhost/GROUPPROJECT_GROUP24/google.php">Google</option>
            </select>
        </label>
    </div>
</nav>

<script>
window.onload = function() {
    const storedPage = localStorage.getItem('page');
    const dropdown = document.getElementById('redirectDropdown');

    if (storedPage && dropdown) {
        dropdown.value = storedPage;
    }
};

function selectRedirect() {
    const select = document.getElementById("redirectDropdown");
    const selectedValue = select.value;

    if (selectedValue) {
        localStorage.setItem('page', selectedValue);
        window.location.href = selectedValue;
    }
}
</script>