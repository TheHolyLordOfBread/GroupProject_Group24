<nav class="navbar">

        <link rel="stylesheet" href="css/style.css?">

    <div class="logo" >
    <label> select a page:
          <select  id="redirectDropdown" value="Select A Page" onChange="selectRedirect()">
            <option value="http://localhost/GROUPPROJECT_GROUP24/Index.php">Combined Graphs</option>
            <option value="http://localhost/GROUPPROJECT_GROUP24/nahrim-map.php">Nahrim Map</option>
            <option value="http://localhost/GROUPPROJECT_GROUP24/open_meteo.php">Open Meteo Map</option>
            <option value="">Google</option>
          </select>
    </label>




      <script>
        window.onload = function()
        {
            const storedpage = localStorage.getItem('page')

            document.getElementById('redirectDropdown').value=storedpage;
        }



        function selectRedirect() {
          const select = document.getElementById("redirectDropdown");
          const selectedValue = select.value;

          

          if (selectedValue) 
          {
          localStorage.setItem('page',selectedValue)
          window.location = selectedValue; // Redirects the page
          }
          
        }
      </script>




        
    </div>

</nav>