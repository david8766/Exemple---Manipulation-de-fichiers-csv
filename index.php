<?php
echo "<h1><u>Test CSV :</u></h1>";
echo "<h2>1 - Afficher les données d'un fichier CSV dans un tableau : <h2>";

$filePath = 'http://localhost/www/CSV/clients.csv';
// Lire le contenu d'un fichier csv dans un tableau.
$file = file($filePath);
// total de lignes du tableau
$totalLigns = count($file);
// Retourne chaque ligne de chaines de caractères en tableau. 
$LignsArr = [];
for ($i=0; $i < $totalLigns ; $i++) { 
    $lignsArr[$i] = explode(";", $file[$i]);
}
// Afficher les données dans un tableau.
echo '<table border=1>';
echo "<caption>Tableau de clients</caption><tbody>";
foreach ($lignsArr as $lign) {  
    echo "<tr>";
    foreach ($lign as $value) {
        echo "<td>" . $value . "</td>";       
    } 
    echo "</tr>"; 
}
echo "</tbody></table>";
echo "<hr>";
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
echo "<h2>2 - Insérer les données du fichier CSV dans une base de données : <h2>";
// Récupération des champs de la base.
$fields = $lignsArr[0];
$totalFields = count($fields);;
// Suppression de la première ligne du tableau.
unset($lignsArr[0]);
// Réindexation du tableau.
$customers = array_values($lignsArr);
// Configuration de variables pour la connexion à la base de données.
$dsn = 'mysql:host=localhost;port=3306;dbname=test_csv;charset=utf8';
$user = 'root';
$password = '';
// Connexion à la base de données via PDO
try {
    $db = new PDO($dsn,$user,$password);
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connexion échouée : ' . $e->getMessage());
}
// Vérication da la table clients
$stmt = $db->query("SELECT * FROM clients");
$res= $stmt->fetchAll(PDO::FETCH_ASSOC);
// Si la table client est vide on enregistre les données sinon on affiche les données.
if(empty($res)){
    foreach ($customers as $customer) {
        $sql = "INSERT INTO `clients` (`id`,`nom`,`prenom`,`email`,`tel`,`adresse`,`ville`,`code_postal`)
        VALUES(:id, :nom, :prenom, :email, :tel, :adresse, :ville, :code_postal )";
        $statement = $db->prepare($sql);
        $statement->bindValue(':id',$customer[0]);
        $statement->bindValue(':nom',$customer[1]);
        $statement->bindValue(':prenom',$customer[2]);
        $statement->bindValue(':email',$customer[3]);
        $statement->bindValue(':tel',$customer[4]);
        $statement->bindValue(':adresse',$customer[5]);
        $statement->bindValue(':ville',$customer[6]);
        $statement->bindValue(':code_postal',$customer[7]);
        $insertIsOk = $statement->execute();
        if($insertIsOk){
            $message = "Le client portant l'identifiant n°" . $customer[0] . ' a bien été enregistrée dans la base de données.';
        }else{
            $message = "Erreur de l'insertion dans la base de données.";
        }
        echo $message . "<br>";
    }   
}else{
    // Afficher les données dans un tableau.
    echo '<table border=1>';
    echo "<caption>Tableau de clients</caption><tbody>";
    echo "<tr>"; 
    foreach ($fields as $value) {
        echo "<td>" . $value . "</td>";
    }
    echo "</tr>"; 
    foreach ($res as $lign) {  
        echo "<tr>";
        foreach ($lign as $value) {
            echo "<td>" . $value . "</td>";       
        } 
        echo "</tr>"; 
    }
    echo "</tbody></table>";
    echo "<hr>";
}
echo "<hr>";
//------------------------------------------------------------------------------------------------------------------------------------------------------
echo "<h2>3 - Ajouter une ligne dans la table et dans le fichier CSV : <h2>";
$stmt = $db->query("SELECT * FROM clients WHERE id=101");
$res= $stmt->fetch(PDO::FETCH_ASSOC);
// Si le client n°101 existe j'informe l'utilisateur sinon je l'enregistre dans la base de donnée et le fichier csv.
if($res == false){
    // Ajout d'un client n°101 dans la base de données.
    $sql = "INSERT INTO `clients` (`id`,`nom`,`prenom`,`email`,`tel`,`adresse`,`ville`,`code_postal`)
    VALUES('101', 'Morris', 'Philip', 'Morris@gmail.com', '04 25 26 23 89', '15 rue des palmiers', 'Paris', '75018')";
    $stmt = $db->prepare($sql);
    $insertIsOk = $stmt->execute();
    if($insertIsOk){
        echo "Le client portant l'identifiant 101 a bien été ajouté dans la base de données: <br>";   
        // Ajout du client n°101 dans le fichier CSV.
        $stmt = $db->query("SELECT * FROM clients WHERE id=101");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        // Afficher les données dans un tableau.
        echo '<table border=1>';
        echo "<tbody>";
        echo "<tr>"; 
        foreach ($fields as $value) {
            echo "<td>" . $value . "</td>";
        }
        echo "</tr>"; 
        echo "<tr>"; 
        foreach ($res as $value) {     
            echo "<td>" . $value . "</td>";           
        }
        echo "</tr>";
        echo "</tbody></table>";
        echo '<br>';
        // Insérer les données dans une nouvelle ligne dans le fichier CSV
        $data = implode(';',$res);
        $data = $data . PHP_EOL;
        $insertInCSV = file_put_contents('clients.csv',$data,FILE_APPEND);
    }else{
        echo "Erreur lors de l'insertion dans la base de données.";
    }    
}else{
    echo "Le client n°101 a déjà été ajouté dans la base de données<br>";
}
$stmt = $db->query("SELECT * FROM clients WHERE id=102");
$res= $stmt->fetch(PDO::FETCH_ASSOC);
// Si le client n°102 existe j'informe l'utilisateur sinon je l'enregistre dans la base de donnée et le fichier CSV.
if($res == false){
    // Ajout du client n°102 dans la base de données.
    $sql = "INSERT INTO `clients` (`id`,`nom`,`prenom`,`email`,`tel`,`adresse`,`ville`,`code_postal`)
    VALUES('102', 'Smith', 'Jhon', 'Smith@gmail.com', '04 25 89 63 89', '15 rue des platanes', 'Paris', '75003')";
    $stmt = $db->prepare($sql);
    $insertIsOk = $stmt->execute();
    if($insertIsOk){
        echo "Le client portant l'identifiant 102 a bien été ajouté dans la base de données.";
        // Ajout du client n°102 dnas le fichier CSV.
        $stmt = $db->query("SELECT * FROM clients WHERE id=102");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        // Afficher les données dans un tableau.
        echo '<table border=1>';
        echo "<tbody>";
        echo "<tr>"; 
        foreach ($fields as $value) {
            echo "<td>" . $value . "</td>";
        }
        echo "</tr>"; 
        echo "<tr>"; 
        foreach ($res as $value) {     
            echo "<td>" . $value . "</td>";           
        }
        echo "</tr>";
        echo "</tbody></table>";
        echo '<br>'
        // Insérer les données dans une nouvelle ligne dans le fichier CSV
        $data = implode(';',$res);
        $data = $data . PHP_EOL;
        $insertInCSV = file_put_contents('clients.csv',$data,FILE_APPEND);
    }else{
        echo "Erreur lors de l'insertion dans la base de données.";
    }  
}else{
    echo "Le client n°102 a déjà été ajouté dans la base de données<br>";
}
echo "<hr>";
//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
echo "<h2>4 - Supprimer une ligne dans la table et dans le fichier CSV : <h2>";
// Suppression du client n°101:
// Récupération des données de ce client.
$stmt = $db->query("SELECT * FROM clients WHERE id=101");
$res = $stmt->fetch(PDO::FETCH_ASSOC);
$data = implode(';',$res);
$data = $data . PHP_EOL;
// Suppression des donées dans la base de données.
$sql = 'DELETE FROM `clients` WHERE id = 101';
$deleteIsOk = $db->query($sql);
// Réécriture du fichier en supprimant la ligne correspondate aux données de ce client.
$file = file($filePath);
foreach($file as $num => $lign){  
    if ($lign == $data) {
        unset($file[$num]);
        $handle = fopen('clients.csv', 'w+'); 
        fwrite($handle, implode('',$file));
        fclose($handle);
        echo "Le client n°101 a bien été supprimé de la base de données et du fichier CSV.";   
    }     
}
echo "<hr>";
echo "<h2>5 - Modifier une ligne dans la table et dans le fichier CSV : <h2>";
// Modification du client n°102:
// Récupération des données de ce client avant la mise à jour.
$stmt = $db->query("SELECT * FROM clients WHERE id=102");
$res = $stmt->fetch(PDO::FETCH_ASSOC);
$data = implode(';',$res);
$data = $data . PHP_EOL;
// Mettre à jour l'email du client
$stmt = $db->query("SELECT `email` FROM clients WHERE id=102");
$res = $stmt->fetch(PDO::FETCH_ASSOC);
$email = "JhonSmith@gmail.com";
if($res['email'] != $email){
    // Mise à jour des données du client dans la base de données
    $sql = "UPDATE `clients` SET email=:email where id= 102";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':email',$email);
    $updateIsOk = $stmt->execute();
    if($updateIsOk){
        // Récupération des données de ce client après la mise à jour.
        $stmt = $db->query("SELECT * FROM clients WHERE id=102");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $data2 = implode(';',$res);
        $data2 = $data2 . PHP_EOL;
    
        // Réécriture du fichier CSV en modifiant la ligne correspondate aux données de ce client.
        $file = file($filePath);
        foreach($file as $num => $lign){
            if ($lign == $data) {
                unset($file[$num]);
                $file[$num] = $data2;
                $handle = fopen('clients.csv', 'w+'); 
                fwrite($handle, implode('',$file));
                fclose($handle);
                echo "Le client n°102 a bien été modifié dans la base de données et dans le fichier CSV.<br>";   
            }     
        }
    }else{
        echo "Erreur de la mise à jour dans la base de données.";
    }
}else{
    echo "modifier l'email pour l'enregistrer";
}
?>