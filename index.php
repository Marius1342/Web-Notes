<html>
<?php

require_once("db.php");

?>

<head>
    <title>Web Notes</title>
    <meta http-equiv="expires" content="0">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link content="no-cache" rel="stylesheet" href="style.css">

</head>

<body>
    <div>
        <?php
        //Delete Pr
        if (isset($_GET["id"]) && isset($_GET["del"])) {
            //Delete all notes
            $db = new DB();
            $SQL = "DELETE FROM tasks WHERE idofpr=:id";
            $conn = $db->conn->prepare($SQL);
            $conn->bindParam(":id", $_GET["id"]);
            try {
                $conn->execute();
            } catch (PDOException $ex) {
                echo $ex->getMessage();
                exit();
            }
            //Delete the main pr
            $db = new DB();
            $SQL = "DELETE FROM projects WHERE id=:id";
            $conn = $db->conn->prepare($SQL);
            $conn->bindParam(":id", $_GET["id"]);
            try {
                $conn->execute();
            } catch (PDOException $ex) {
                echo $ex->getMessage();
                exit();
            }
            echo "The project was deleted";
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                $url = "https://";
            else
                $url = "http://";
            // Append the host(domain name, ip) to the URL.   
            $url .= $_SERVER['HTTP_HOST'];

            // Append the requested resource location to the URL   
            $url .= $_SERVER['REQUEST_URI'];

            header("Location: " . strtok($url, '?'));
        }

        //Delete a note
        if (isset($_GET["id_"])) {
            $db = new DB();
            $SQL = "DELETE FROM tasks WHERE id=:id";
            $conn = $db->conn->prepare($SQL);
            $conn->bindParam(":id", $_GET["id_"]);
            try {
                $conn->execute();
            } catch (PDOException $ex) {
                echo $ex->getMessage();
                exit();
            }
        }


        //New Note
        if (isset($_GET["note"]) && isset($_GET["name"]) && isset($_GET["id"]) && isset($_GET["v"])) {
            $db = new DB();
            $SQL = "SELECT * FROM tasks WHERE name=:name AND idofpr=:idofpr";
            $conn = $db->conn->prepare($SQL);
            $conn->bindParam(":name", $_GET["name"]);
            $conn->bindParam(":idofpr", $_GET["id"]); //idofpr
            try {
                $conn->execute();
            } catch (PDOException $ex) {
                echo $ex->getMessage();
                exit();
            }
            $res = $conn->fetchAll(PDO::FETCH_ASSOC);
            if (count($res) > 0) {
                echo "You have this note already";
            } else {

                $SQL = "INSERT INTO tasks (idofpr, name,contents) VALUES (:idofpr, :name,:contents )";
                $conn = $db->conn->prepare($SQL);
                $conn->bindParam(":idofpr", $_GET["id"]);
                $conn->bindParam(":name", $_GET["name"]);
                $conn->bindParam(":contents", $_GET["v"]);
                try {
                    $conn->execute();
                } catch (PDOException $ex) {
                    echo $ex->getMessage();
                    exit();
                }
            }
        }


        //New Project
        if (isset($_GET["name"]) && isset($_GET["description"])) {
            if ($_GET["name"] == "") {
                echo "Enter a name for the project";
                exit();
            }



            $db = new DB();

            $SQL = "SELECT * FROM projects WHERE name=:name";

            $conn = $db->conn->prepare($SQL);
            $conn->bindParam(":name", $_GET["name"]);
            $conn->execute();
            $result = $conn->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) > 0) {
                echo "The project was created";
            } else {
                $SQL = "INSERT INTO projects (name, description) VALUES (:name, :description)";
                $conn = $db->conn->prepare($SQL);
                $conn->bindParam(":name", $_GET["name"]);
                $conn->bindParam(":description", $_GET["description"]);
                try {
                    $conn->execute();
                } catch (PDOException $ex) {
                    echo $ex->getMessage();
                    exit();
                }
            }
        }
        //Show all projects
        if (!isset($_GET["id"])) {
            echo "<table>
        <tr id='legend'>
        <th>Name</th>
        <th>Description</th>
        <tr>
        ";

            $db = new DB();

            $SQL = "SELECT * FROM projects";
            $conn = $db->conn->prepare($SQL);
            try {
                $conn->execute();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                exit();
            }
            // set the resulting array to associative
            $result = $conn->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                echo "<tr class='colum'><th><a class='link-id' href='?id=" . $row["id"] . "'>" . $row["name"] . "</th><th>" . $row["description"] . "</th></tr>";
            }
            echo "</table>";
        }
        //Show the Tasks of the project to
        else if (isset($_GET["id"])) {
            $db = new DB();

            echo "<table>
        <tr id='legend'>
        <th>Name</th>
        <th>Contents</th>
        <tr>
        ";
            $SQL = "SELECT * FROM tasks WHERE idofpr=:idofpr";
            $conn = $db->conn->prepare($SQL);
            $conn->bindParam(":idofpr", $_GET["id"]);
            try {
                $conn->execute();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                exit();
            }
            $result = $conn->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                echo "<tr class='colum'><th><p class='notes' onclick='del_note(" . $row["id"] . ");'/>" . $row["name"] . "</th><th>" . $row["contents"] . "</th></tr>";
            }
            echo "</table>";
        }










        echo "</div>";

        if (!isset($_GET["id"])) : ?>
        <div class="new">
            <label class="heading_label" for="newPr">New project</label>
            <form autocomplete="off" method="GET" name="newPr">
                <div>

                    <label class="label_input" for="name">Name</label>
                    <div class="in_div">
                        <input class="input_class" name="name" type="text" placeholder="The name of your project"
                            required>
                    </div>

                    <label class="label_input" for="description">Description</label>
                    <div class="in_div">
                        <input class="input_class" name="description" type="text" placeholder="The description"
                            required>
                    </div>
                    <br>
                    <input type="submit" class="submite_bnt" value="Create">
                </div>
            </form>
        </div>
        <?php
        else : ?>
        <div class="new">
            <label class="heading_label" for="newPr">New note</label>
            <form autocomplete="off" method="GET" name="newPr">
                <label class="label_input" for="name">Name</label>
                <div class="in_div">
                    <input class="input_class" name="name" type="text" required>
                </div>
                <label class="label_input" for="v">Contents</label>
                <div class="in_div">
                    <input class="input_class" name="v" type="text">
                </div>
                <input hidden type="text" name="id" value="<?php echo $_GET["id"]; ?>">
                <input hidden type="text" name="note">
                <br>
                <input type="submit" class="submite_bnt" value="Create">
            </form>
        </div>
        <div>
            <br>
            <button id="delete" class="del_bnt" onclick="DEL();">Delete project</button>

            <form method="GET" id="fr">
                <input hidden type="text" name="id" value="<?php echo $_GET["id"]; ?>">
                <input hidden type="text" name="del">
                <input hidden type="submite">
            </form>

            <form method="GET" id="del_n">
                <input hidden type="text" id="del_n_id" name="id_" value="">
                <input hidden type="text" name="id" value="<?php echo $_GET["id"]; ?>">
                <input hidden type="submite">
            </form>


            <script>
            function del_note(id) {
                if (confirm("Do you really want to delete this note?")) {
                    document.getElementById("del_n_id").value = id;
                    document.getElementById("del_n").submit();
                }
            }

            function DEL() {

                if (confirm("Do you really want to delete this project?")) {
                    document.getElementById("fr").submit();
                }

            }
            </script>
        </div>
        <?php endif;

        if (isset($_GET["id"])) {
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                $url = "https://";
            else
                $url = "http://";
            // Append the host(domain name, ip) to the URL.   
            $url .= $_SERVER['HTTP_HOST'];

            // Append the requested resource location to the URL was
            $url .= $_SERVER['REQUEST_URI'];

            echo "<div id='div'><br><a class='return_bnt bnt' href='" . strtok($url, '?') . "'>Back to the main Page</a></div>";
        }
        ?>

</body>

</html>
