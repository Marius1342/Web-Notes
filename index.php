<html>
<?php

require_once("db.php");

?>

<head>
    <title>Web Notes</title>
    <meta http-equiv="expires" content="0">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta charset="UTF-8">
    <link content="no-cache" rel="stylesheet" href="style.css?a=aa">

</head>

<body>
    <div>
        <?php

        function Escape($text)
        {
            return htmlspecialchars($text, ENT_QUOTES);
        }

        //Edit description
        if (isset($_POST["id_pr"]) && isset($_POST["new_desc"])) {
            if ($_POST["id_pr"] == "" || $_POST["new_desc"] == "") {
                echo "Edit description every param must be set";
                exit();
            }
            $db = new DB();
            $SQL = "UPDATE projects SET description=:description WHERE id=:id";
            $conn = $db->conn->prepare($SQL);
            $_POST["new_desc"] = Escape($_POST["new_desc"]);
            $conn->bindParam(":description", $_POST["new_desc"]);
            $conn->bindParam(":id", $_POST["id_pr"]);
            try {
                $conn->execute();
            } catch (PDOException $ex) {
                echo $ex->getMessage();
                exit();
            }
        }

        //Edit note
        if (isset($_POST["text"]) && isset($_POST["edit"])) {
            if ($_POST["text"] == "" || $_POST["edit"] == "") {
                echo "Error update";
            } else {
                $db = new DB();
                $SQL = "UPDATE tasks SET contents=:contents WHERE id=:id";
                $conn = $db->conn->prepare($SQL);
                $_POST["text"] =  Escape($_POST["text"]);
                $conn->bindParam(":contents", $_POST["text"]);
                $conn->bindParam(":id", $_POST["edit"]);
                try {
                    $conn->execute();
                } catch (PDOException $ex) {
                    echo $ex->getMessage();
                    exit();
                }
            }
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
            $SQL = "SELECT * FROM tasks WHERE name=:name AND idofpr=:idofpr";
            $conn = $db->conn->prepare($SQL);
            $_GET["name"] = Escape($_GET["name"]);
            $_GET["v"] = Escape($_GET["v"]);
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
            $_GET["name"] = Escape($_GET["name"]);
            $_GET["description"] = Escape($_GET["description"]);

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
                echo "<tr class='colum'><th><a class='link-id' href='?id=" . $row["id"] . "'>" . $row["name"] . "</th><th><p id='" . $row["id"] . "'>" . $row["description"] . "</p> <a class='edit' onclick='edit(" . $row["id"] . ", this);' >Edit</a> </th></tr>";
            }
            echo "</table>";
        }
        //Show the Tasks of the project 
        else if (isset($_GET["id"])) {
            $db = new DB();
            $SQL = "SELECT name FROM projects WHERE id=:id";
            $conn = $db->conn->prepare($SQL);
            $conn->bindParam(":id", $_GET["id"]);
            try {
                $conn->execute();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                exit();
            }
            $result = $conn->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) == 0) {
                echo "Cant find project";
                exit();
            }
            $title = $result[0]["name"];



            echo "<table id='table_'>
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
                echo "<tr class='colum'><th><p class='notes' onclick='del_note(" . $row["id"] . ");'/>" . $row["name"] . "</th><th><p id='" . $row["id"] . "' class='item_id' >" . $row["contents"] . "</p> <a class='edit' onclick='edit(" . $row["id"] . ", this);' >Edit</a></th></tr>";
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
                            <input class="input_class" name="name" type="text" placeholder="The name of your project" required>
                        </div>

                        <label class="label_input" for="description">Description</label>
                        <div class="in_div">
                            <input class="input_class" name="description" type="text" placeholder="The description" required>
                        </div>
                        <br>
                        <input type="submit" class="submite_bnt" value="Create">
                    </div>
                </form>
                <form method="POST" id="edit_f">
                    <input hidden type="text" id="edit_fid" name="id_pr" value="">
                    <input hidden type="text" id="edit_ftext" name="new_desc" value="">
                    <input hidden type="submite">
                </form>
                <script>
                    function edit(id, caller) {
                        //Save
                        if (caller.innerText != "Edit") {
                            document.getElementById("edit_fid").value = document.getElementById(id).id;
                            document.getElementById("edit_ftext").value = document.getElementById(id).innerText;
                            document.getElementById("edit_f").submit();
                        }
                        //Set edit mode
                        else {
                            document.getElementById(id).contentEditable = true;
                            caller.innerText = "Save";
                        }

                    }
                </script>
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

                <form method="POST" id="edit_f">
                    <input hidden type="text" id="edit_fid" name="edit" value="">
                    <input hidden type="text" id="edit_ftext" name="text" value="">
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

                    function edit(id, caller) {
                        //Save
                        if (caller.innerText != "Edit") {
                            document.getElementById("edit_fid").value = document.getElementById(id).id;
                            document.getElementById("edit_ftext").value = document.getElementById(id).innerText;
                            document.getElementById("edit_f").submit();
                        }
                        //Set edit mode
                        else {
                            document.getElementById(id).contentEditable = true;
                            caller.innerText = "Save";
                        }

                    }

                    document.title = "<?php echo $title; ?>";
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
        <!--
        Auto refresh
        !-->
        <?php
        if (REFRESH == true && isset($_GET["id"])) :
        ?>

            <script>
                var DEBUG = false;
                const SecondsToMilliseconds = 1000;
                var items_cl = document.getElementsByClassName("colum");
                var items = document.getElementsByClassName("item_id");


                function RemoveById(id) {
                    for (let i = 0; i < items.length; i++) {
                        if (id == items[i].id) {
                            items[i].remove();
                            items_cl[i].remove();
                            return;
                        }

                    }


                }

                function IdGetPosition(id, arr) {
                    for (let i = 0; i < arr.length; i++) {
                        if (id == arr[i]) {
                            return i;
                        }

                    }
                    console.error("Out of range:" + id + " " + arr);
                    return -1;
                }

                function IdExistArray(id, arr) {
                    for (let i = 0; i < arr.length; i++) {
                        if (id == arr[i]) {
                            return true;
                        }

                    }
                    return false;
                }

                function HtmlCollectionToArrayId(HtmlCollection) {
                    var arr = [];
                    for (let i = 0; i < HtmlCollection.length; i++) {
                        arr.push(HtmlCollection[i].id);
                    }
                    return arr;
                }

                function AddItem(html) {
                    var tbody = document.getElementsByClassName("colum")[document.getElementsByClassName("colum").length - 1];
                    tbody.insertAdjacentHTML('afterend', html);

                }
                setInterval(() => {
                    items_cl = document.getElementsByClassName("colum");
                    items = document.getElementsByClassName("item_id");
                    fetch("API.php?id=" + <?php echo $_GET["id"]; ?>).then(data => data.json()).then(

                        (data) => {
                            if (data["Status"] != "202") {
                                console.error("CANT FETCH:" + data["Status"]);
                                return;
                            } else {
                                console.log(data["Length"])
                                var ids = HtmlCollectionToArrayId(items);
                                var DeleteIds = HtmlCollectionToArrayId(items);
                                var toAddIds = [];
                                for (let i = 0; i < data["id"].length; i++) {
                                    if (IdExistArray(data["id"][i], ids) == false) {
                                        toAddIds.push(data["id"][i]);
                                    } else if (IdExistArray(data["id"][i], ids) == true) {
                                        var id = IdGetPosition(data["id"][i], DeleteIds);
                                        DeleteIds.splice(id, 1);

                                    }
                                }
                                if (DEBUG) {
                                    console.log(DeleteIds + ":" + toAddIds);
                                }
                                //Delete node
                                for (let i = 0; i < DeleteIds.length; i++) {
                                    RemoveById(DeleteIds[i]);
                                }

                                //Add new node
                                for (let i = 0; i < toAddIds.length; i++) {
                                    fetch("API.php?id=" + <?php echo $_GET["id"]; ?> + "&nd=" + toAddIds[i]).then((
                                        data) => data.json()).then((data) => {
                                        if (data["Status"] == "202") {
                                            AddItem(data["tr"]);
                                        } else {
                                            console.error("CANT FETCH add node");
                                        }

                                    })


                                }

                            }
                        }
                    )



                }, <?php echo time; ?> * SecondsToMilliseconds)
            </script>


        <?php
        endif;
        ?>
</body>

</html>