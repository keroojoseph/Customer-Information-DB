<?php

global $connection;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'Employees.php';
include_once 'db.php';
include_once 'AbstractModel.php';


global $id;

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (is_numeric($id) && $id > 0) {
        $user = Employees::getByPk($id);
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (is_numeric($id) && $id > 0) {
        $user = Employees::getByPk($id);
        if ($user->delete() === true) {
            $msg = 'Deleted successfully';
        }
    }
}

try {
    if (isset($_POST['submit'])) {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT);

        if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            if (is_numeric($id) && $id > 0) {
                $user = Employees::getByPk($id);
                $user->name = $name;
                $user->email = $email;
                $user->address = $address;
                $user->phone = $phone;
            }
        } else {
            $user = new Employees($name, $email, $address, $phone);
        }

//        $stm = $connection->prepare($sql);

        if ($user->save() === true) {
            $msg = 'Employee added successfully';
        } else {
            $error = true;
            $msg = 'An error occurred while adding employee.';
        }
    }
} catch (PDOException $e) {
    $error = true;
    $msg = 'Database error';
}


$result = Employees::getAll();
?>

<!DOCTYPE thml>
<html lang="en">
<head>
    <title>PDO</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
<div class="container">
    <form method="post">
        <p class="msg" <?= isset($error) ? 'error' : '' ?> ><?= $msg ?? '' ?></p>
        <fieldset>
            <legend>Customer Information</legend>
            <table>
                <tr>
                    <td>
                        <label>
                            <input type="text" name="name" placeholder="Write name" required value="<?= $user->name ?? '' ?>" autocomplete="off" >
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>
                            <input type='email' name='email' placeholder="Write email" required value="<?= $user->email ?? '' ?>" autocomplete="off">
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>
                            <input type="text" name="address" placeholder="Write address" required value="<?= $user->address ?? '' ?>"  >
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>
                            <input type="tel" name="phone" placeholder="Write phone" required maxlength="11" value="<?= $user->phone ?? '' ?>" autocomplete="off">
                        </label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" value="Save" name="submit">
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>

    <div class="employees">
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Controller</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (false !== $result) {
                foreach ($result

                         as $row) { ?>
                    <tr>
                        <td><?= $row->name ?> </td>
                        <td><?= $row->email ?> </td>
                        <td><?= $row->address ?> </td>
                        <td><?= $row->phone ?> </td>
                        <td>
                            <a href="/?action=edit&id=<?= $row->customer_id ?>">Edit</a> |
                            <a href="/?action=delete&id=<?= $row->customer_id ?>" onclick="if(!confirm('Do you want to delete this employee')) return false;">Delete</a>
                        </td>
                    </tr>

                <?php }
            } else { ?>
                <td colspan="4"><p>Sorry, Not Found Employees To List</p></td>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
