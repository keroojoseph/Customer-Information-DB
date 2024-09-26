<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'Employees.php';
$connection = null;

try {
    $connection = new PDO('mysql:host=model.com;dbname=estore;port=3306;charset=utf8mb4', 'root', 'Keroo@30311152404778', array
    (PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (PDOException $e) {
    echo('Sorry, something went wrong.');
}

global $id;

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (is_numeric($id) && $id > 0) {
        $sql = 'SELECT * FROM customer WHERE customer_id = ?';
        $stmt = $connection->prepare($sql);
        $foundUser = $stmt->execute([$id]);
        if ($foundUser === true) {
            $user = ($stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Employees', array('name', 'email')));
            $user = array_shift($user);
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (is_numeric($id) && $id > 0) {
        $sql = 'Delete FROM customer WHERE customer_id = ?';
        $stmt = $connection->prepare($sql);
        $foundUser = $stmt->execute([$id]);
        if ($foundUser === true) {

        }
    }
}

try {
    if (isset($_POST['submit'])) {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT);
        $employee = new Employees($name, $email);
        $employee->address = $address;
        $employee->phone = $phone;
        $arr = [$name, $email, $address, $phone];

        if (isset($user)) {
            $sql = 'UPDATE customer SET name = ?, email = ? , address = ?, phone = ? where customer_id = ?';
            $arr[4] = $id;
        } else {
            $sql = 'INSERT INTO customer (name, email, address, phone) VALUES (?, ?, ?, ?)';
        }

        $stm = $connection->prepare($sql);

        if ($stm->execute($arr)) {
            $msg = 'Employee added';
        } else {
            $error = true;
            $msg = 'An error occurred while adding employee.';
        }
    }
} catch (PDOException $e) {
    $error = true;
    $msg = 'Database error';
}

$sql = 'SELECT * FROM customer';
$stm = $connection->Query($sql);
$result = $stm->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Employees', array('name', 'email'));
$result = ((is_array($result) && !empty($result))? $result : false);
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
