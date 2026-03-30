// Select all checkboxes with the name 'Source' using querySelectorAll.
var checkboxes = document.querySelectorAll("input[type=checkbox][name=Source]");
let enabledSources = []



checkboxes.forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        enabledSettings =
            Array.from(checkboxes) 
                .filter(i => i.checked) 
                .map(i => i.value) // returns only the selected checkboxes values

        console.log(enabledSettings)
        hideDivs()
    })
})

function hideDivs() {

    var divs = document.querySelectorAll("div.box");
    divs.forEach(function (div) {



        if (enabledSettings.includes(div.id)) {
            div.style.display = "";
        } else {
            div.style.display = "none";
        }



    });



}

