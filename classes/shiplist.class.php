<?PHP

	class ShipList
	{
		private $userId;
		private $entityId;
		private $ships;
		
		function ShipList($entityId,$userId)
		{
			$this->userId = $userId;
			$this->entityId = $entityId;
		}
		
		function count($item=null)
		{
			$res = dbquery("
			SELECT
				COUNT(shiplist_id)
			FROM
				shiplist
			WHERE
				shiplist_user_id=".$this->userId ."
				AND shiplist_entity_id=".$this->entityId."
				".($item>0 ? " AND shiplist_ship_id=".$item."" : "")."
			;");
			$arr = mysql_fetch_row($res);
			return $arr[0];
		}
		
		function add($shipId,$cnt)
		{
			dbquery("
				UPDATE
					shiplist
				SET
					shiplist_count=shiplist_count+".max($cnt,0)."
				WHERE
					shiplist_user_id='".$this->userId."'
					AND shiplist_entity_id='".$this->entityId."'
					AND shiplist_ship_id='".$shipId."';
			");			
			if(mysql_affected_rows()==0)
			{
				dbquery("
					INSERT INTO
					shiplist
					(
						shiplist_user_id,
						shiplist_entity_id,
						shiplist_ship_id,
						shiplist_count
					)
					VALUES
					(
						'".$this->userId."',
						'".$this->entityId."',
						'".$shipId."',
						'".max($cnt,0)."'
					);
				");
			}
		}
		
		function remove($shipId,$cnt)
		{
			$res = dbquery("SELECT 
								shiplist_id, 
								shiplist_count 
							FROM 
								shiplist 
							WHERE 
								shiplist_ship_id=".$shipId." 
								AND shiplist_user_id='".$this->userId."' 
								AND shiplist_entity_id='".$this->entityId."';");
			$arr = mysql_fetch_row($res);

			$delable = min($cnt,$arr[1]);
			
			dbquery("UPDATE
				shiplist
			SET
				shiplist_count = shiplist_count - ".$delable."
			WHERE 
				shiplist_ship_id=".$shipId."
				AND shiplist_id='".$arr[0]."';");

			return $delable;
		}
	
	
	}


?>