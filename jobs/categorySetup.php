<?php
$request = [
    'sqlname' => 'localhost',
    'username' => 'i12reader',
    'password' => 'Y1>03$5&,}5b7/G',
    'db' => 'bkdata_486',
    'products' => 'bf_categories',
];

$connect = mysqli_connect($request['sqlname'], $request['username'], $request['password'], $request['db']) or die(mysql_error());

function getRowTop($id) {
    global $connect;
    
    $query = "SELECT * FROM bf_categories WHERE amazon_id = ".(int)$id;

    $results = mysqli_query($connect, $query) or die(mysqli_error($connect));
    if (!mysqli_num_rows($results)) {
        return null;
    }
    $result = mysqli_fetch_array($results);
    if ($result['parent_id'] == 0) {
        return $result;
    }

    return getRowTop($result['parent_id']);
}

$query = "SELECT bf_books.ean, bf_books_nodes.node_id FROM bf_books LEFT JOIN bf_books_nodes ON bf_books.ean = bf_books_nodes.ean WHERE top_category IS NULL GROUP BY bf_books.ean LIMIT 1000";
$result = mysqli_query($connect, $query);

while($row = mysqli_fetch_assoc($result)) {
    $top = getRowTop($row['node_id']);
    if (!empty($top)) {
        mysqli_query($connect, "UPDATE bf_books SET top_category='".$top['amazon_id']."' WHERE ean = '".$row['ean']."' LIMIT 1") or die(mysqli_error($connect));
    } else {
        mysqli_query($connect, "UPDATE bf_books SET top_category='0' WHERE ean = '".$row['ean']."' LIMIT 1") or die(mysqli_error($connect));
    }
}

mysqli_close($connect);