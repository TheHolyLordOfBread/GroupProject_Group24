
<nav class="navbar">

    <div class="logo">
      <select id="redirectDropdown" value="Select A Page" onChange="selectRedirect()">
        <option value="http://localhost/GROUPPROJECT_GROUP24/Index.php">Combined Graphs</option>
        <option value="http://localhost/GROUPPROJECT_GROUP24/nahrim-map.php">Nahrim Map</option>
        <option value="http://localhost/GROUPPROJECT_GROUP24/open_meteo.php">Open Meteo Map</option>
        <option value="">Google</option>
      </select>

      <script>
        function selectRedirect() {
          const select = document.getElementById("redirectDropdown");
          const selectedValue = select.value;
          if (selectedValue) {
          window.location = selectedValue; // Redirects the page
          }
          
        }
      </script>




        
    </div>
</nav>