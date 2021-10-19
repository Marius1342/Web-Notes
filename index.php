<html>
<?php

require_once("db.php");

?>

<head>
    <title>Web Notes</title>
    <style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td,
    th {
        border: 3px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    .new {
        margin-top: 3em;
    }

    table tr:hover {
        background-color: #ddd;

    }

    .newpr {
        margin: auto;
        width: 50%;
        padding: 10px;
    }

    .link-id {
        color: green;

    }

    .notes:hover {
        color: rgb(10, 10, 230);
    }
    </style>
</head>

<body>
    <div>
        <?php

        if (isset($_GET["id"])) {
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                $url = "https://";
            else
                $url = "http://";
            // Append the host(domain name, ip) to the URL.   
            $url .= $_SERVER['HTTP_HOST'];

            // Append the requested resource location to the URL was
            $url .= $_SERVER['REQUEST_URI'];

            echo "<a href='" . strtok($url, '?') . "'>Back to the main Page</a>";
        }
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
            $SQL = "SELECT *FROM tasks WHERE name=:name AND idofpr=:idofpr";
            $conn = $db->conn->prepare($SQL);
            $conn->bindParam(":name", $_GET["name"]);
            $conn->bindParam(":idofpr", $_GET["idofpr"]);
            try {
                $conn->execute();
            } catch (PDOException $ex) {
                echo $ex->getMessage();
                exit();
            }
            $res = $conn->fetchAll(PDO::FETCH_ASSOC);
            if (count($res) > 0) {
                echo "You have this note already";
                exit();
            }

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
        <tr>
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
                echo "<tr><th><a class='link-id' href='?id=" . $row["id"] . "'>" . $row["name"] . "</th><th>" . $row["description"] . "</th></tr>";
            }
            echo "</table>";
        }
        //Show the Tasks of the project to
        else if (isset($_GET["id"])) {
            $db = new DB();

            echo "<table>
        <tr>
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
                echo "<tr><th><p class='notes' onclick='del_note(" . $row["id"] . ");'/>" . $row["name"] . "</th><th>" . $row["contents"] . "</th></tr>";
            }
            echo "</table>";
        }










        echo "</div>";

        if (!isset($_GET["id"])) : ?>
        <div class="newpr">
            <label for="newPr">New project</label>
            <form autocomplete="off" method="GET" name="newPr">
                <label for="name">Name</label>
                <br>
                <input name="name" type="text" required>
                <br>
                <label for="description">Description</label>
                <br>
                <input name="description" type="text" required>
                <br>
                <input type="submit" value="Create">
            </form>
        </div>
        <?php
        else : ?>
        <div class="new">
            <label for="newPr">New note</label>
            <form autocomplete="off" method="GET" name="newPr">
                <label for="name">Name</label>
                <br>
                <input name="name" type="text" required>
                <br>
                <label for="v">Contents</label>
                <br>
                <input name="v" type="text">
                <br>
                <input hidden type="text" name="id" value="<?php echo $_GET["id"]; ?>">
                <input hidden type="text" name="note">
                <br>
                <input type="submit" value="Create">
            </form>
        </div>
        <div>
            <button id="delete" onclick="DEL();">Delete project</button>

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
        <?php endif; ?>



</body>

</html>
