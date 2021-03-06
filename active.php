<?php	
//Access: Registered Users
include("variables_file.php");
include("checkUser.php");
echo '
	<!DOCTYPE html>
	<html lang="en">
';
include("views/connection.php");
include("views/header.php");
include("views/navbar.php");
	
	echo '
	<div class="container">
		<h2 id="title">Οι Ενεργοί μου Δανεισμοί</h2>
		<div class="form-inline" id="searchHolder">
			<button type="submit" id="new_borrow" class="btn btn-primary">Αίτηση Νέου Δανεισμού</button><br><br>
		</div>
	';
	
	$borrowQuerySQL = "SELECT * FROM borrow_svds WHERE id_user_borrow = :userToBorrow ORDER BY start_date ASC";
	$borrowQuerySTMT = $db->prepare($borrowQuerySQL);
	$borrowQuerySTMT-> bindParam(':userToBorrow', $id, PDO::PARAM_INT); 
	$borrowQuerySTMT->execute();	
	if ($borrowQuerySTMT->rowCount() == 0){ 
		echo '<p class="alert alert-primary">Δεν έχετε κανέναν ενεργό δανεισμό αυτή τη στιγμή.</p>';
	}else{
		echo '
			<div class="container">
		 		<table id="table" class="table table-bordered">
			    <thead class="thead-dark">
			    <tr>
			   	<th scope="col">Εικόνα</th> 	
				<th scope="col">Ονομασία</th>	
				<th scope="col">Ημ. Έναρξης</th>
				<th scope="col">Ημ. Λήξης</th>
				<th scope="col"></th>
			    </tr>
			    </thead>
		';
		while ($borrowQuerySTMTResult=$borrowQuerySTMT->fetch(PDO::FETCH_ASSOC)) {
			$now = new DateTime();
			$dateToCheck = new DateTime($borrowQuerySTMTResult['expire_date']);
			if($dateToCheck >= $now) {
	    		$borrowState = '<td id="stateIconSuccess" class="fa fa-check" title=Ενεργός></td>';
			}else {
	  			$borrowState ='<td id="stateIconNoSuccess" class="fa fa-power-off" title="Ανενεργός"></td>';
			}
			$idEquipToBorrow = $borrowQuerySTMTResult['id_equip_borrow'];
			$equipBorrowQuerySQL = "SELECT * FROM equip_svds WHERE id_equip = :idEquipToBorrow";
			$equipBorrowQuerySTMT = $db->prepare($equipBorrowQuerySQL);
			$equipBorrowQuerySTMT->bindParam(':idEquipToBorrow', $idEquipToBorrow, PDO::PARAM_INT);
			$equipBorrowQuerySTMT->execute();
			while($equipBorrowQuerySTMTResult=$equipBorrowQuerySTMT->fetch(PDO::FETCH_ASSOC)){
				$equipName = $equipBorrowQuerySTMTResult['name_e'];
			}
			if ($borrowQuerySTMTResult['confirmation_borrow'] == 0) {
				echo '
					<tbody>
					<tr>
					'.$borrowState.'			    
		 			<td>'.$equipName.'</td>
					<td>'.date('d/m/Y',strtotime($borrowQuerySTMTResult['start_date'])).'</td>
					<td>Εκκρεμεί επιβεβαίωση</td>
					<td></td>
					</tr>
					</tbody>
				';
			}else{
				echo '
					<tbody>
					<tr>
					'.$borrowState.'
					<td>'.$equipName.'</td>
					<td>'.date('d/m/Y',strtotime($borrowQuerySTMTResult['start_date'])).'</td>
					<td>'.date('d/m/Y',strtotime($borrowQuerySTMTResult['expire_date'])).'</td>
					<td><a href=borrow_change.php?id_borrow='.$borrowQuerySTMTResult['id_borrow'].' class="fa fa-wrench"></a></td>
					</tr>
					</tbody>
				';	
			}	
		}
		echo '	
			</table>			
			</div>
			</div>	
		';
	}

include("views/footer.php");
echo '
	</body>
	</html>
';
?>	
