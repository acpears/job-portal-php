<?php 

function disactivateAccount($userId, $db, $type){
    if($type == 'seeker'){
        $query_getUser = "SELECT * FROM Seeker s
                        JOIN Seeker_Plan sp ON s.plan_name = sp.name
                        JOIN Wallet w ON s.wallet_id = w.id
                        WHERE s.id = $userId";
    } else if ($type == 'employer'){
        $query_getUser = "SELECT * FROM Employer e
                        JOIN Employer_Plan sp ON e.plan_name = sp.name
                        JOIN Wallet w ON e.wallet_id = w.id
                        WHERE e.id = $userId";
    }
    $user = mysqli_fetch_assoc(mysqli_query($db, $query_getUser));

    $planFee = $user['monthly_cost'];
    $balance = $user['balance'];

    

    // // calculate balance
    // date_default_timezone_set("America/New_York");
    // $date1 = strtotime(date('Y-m-d H:i:s'));
    // $date2 = strtotime($user['last_connected']);
    // $dateDifInSeconds = abs($date2 - $date1);

    // $balance = $wallet['balance'];
    // $balance += ( $dateDifInSeconds * ($planFee/(365*24*60)));

    // $query_updateBalance = "UPDATE Wallet SET balance = $balance WHERE id = $walletId";
    // mysqli_query($db, $query_updateBalance);

    // return true so we disable account
    if($balance > $planFee){
        return true;
    }
    return false;


}

function setTime($userId, $userType, $db){
    date_default_timezone_set("America/New_York");
    $timeStamp = date('Y-m-d H:i:s');
    
    if($userType === "seeker"){
        mysqli_query($db, "UPDATE Seeker SET last_connected = '$timeStamp' WHERE id = $userId");
    } else if ($userType === "employer"){
        mysqli_query($db, "UPDATE Employer SET last_connected = '$timeStamp' WHERE id = $userId");
    } 
}

?>