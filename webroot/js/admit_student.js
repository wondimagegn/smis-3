
var region = Array();

function populateRegions(regionData) {
    for (const id in regionData) {
        region[id] = regionData[id];
    }
}

function addRow(tableID, model, no_of_fields, all_fields, other) {
    var elementArray = all_fields.split(',');
    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
    var cell0 = row.insertCell(0);
    cell0.innerHTML = rowCount;

    for (var i = 1; i <= no_of_fields; i++) {
        var cell = row.insertCell(i);
        var element;

        switch (elementArray[i - 1]) {
            case "region_id":
                element = document.createElement("select");
                var string = '<option value="">--select region--</option>';
                for (var key in region) {
                    string += '<option value="' + key + '">' + region[key] + '</option>';
                }
                element.innerHTML = string;
                break;

            case "exam_year":
                element = document.createElement("select");
                var d = new Date();
                var full_year = d.getFullYear();
                var options = '<option value="">--select year--</option>';
                for (var j = full_year - 1; j > other; j--) {
                    options += '<option value="' + j + '">' + j + '</option>';
                }
                element.innerHTML = options;
                break;

            case "grade":
                element = document.createElement("input");
                element.type = "text";
                element.size = "4";
                break;

            case "mark":
                element = document.createElement("input");
                element.type = "text";
                element.size = "5";
                break;

            case "national_exam_taken":
                element = document.createElement("input");
                element.type = "checkbox";
                break;

            default:
                element = document.createElement("input");
                element.type = "text";
                element.size = "13";
        }

        element.name = "data[" + model + "][" + rowCount + "][" + elementArray[i - 1] + "]";
        cell.appendChild(element);
    }
}

function deleteRow(tableID) {
    try {
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        if (rowCount > 2) {
            table.deleteRow(rowCount - 1);
        }
    } catch (e) {
        alert(e);
    }
}

function updateRegionCity(id) {
    const countryId = $("#country_id_" + id).val();

    const $region = $("#region_id_" + id);
    const $city = $("#city_id_" + id);

    $region.empty().prop('disabled', true);
    $city.empty().prop('disabled', true);

    $.ajax({
        url: '/students/get-regions/' + countryId,
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            $region.prop('disabled', false);
            $region.append($('<option>', { value: '', text: 'Select Region' }));
            $.each(response.regions, function (key, value) {
                $region.append($('<option>', { value: key, text: value }));
            });

            const firstRegion = $region.val();
            if (firstRegion) {
                updateCity(id); // Load cities for the first selected region
            }
        },
        error: function () {
            alert("Could not load regions.");
        }
    });
}

function updateCity(id) {
    const regionId = $("#region_id_" + id).val();
    const $city = $("#city_id_" + id);

    $city.empty().prop('disabled', true);

    $.ajax({
        url: '/students/get-cities/' + regionId,
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            $city.prop('disabled', false);
            $city.append($('<option>', { value: '', text: 'Select City' }));
            $.each(response.cities, function (key, value) {
                $city.append($('<option>', { value: key, text: value }));
            });
        },
        error: function () {
            alert("Could not load cities.");
        }
    });
}
