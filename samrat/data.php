<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch Data from Table</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="script.js"></script>
</head>
<body>
    <button id="fetchDataBtn">Fetch Data</button>
    <div id="dataContainer"></div>
</body>
</html>
<script>
    $(document).ready(function(){
        $.ajax({
            url: 'fatch.php',
            type: 'GET',
            success: function(response){
                $('#dataContainer').html(response);
            },
            error: function(xhr, status, error){
                console.log(xhr.responseText);
            }
        });
});

</script>