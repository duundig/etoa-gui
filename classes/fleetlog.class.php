<?PHP

	class FleetLog extends BaseLog
	{
		
		/**
		 * Others
		 */
		const F_OTHER = 0;
		/**
		 * Launch
		 */
		const F_LAUNCH = 1;
		/**
		 * Cancel
		 */
		const F_CANCEL = 2;
		/**
		 * Action
		 */
		const F_ACTION = 3;
		/**
		 * Return
		 */
		const F_RETURN = 4;
		
		private $fleetId;
		private $userId;
		private $launchtime;
		private $landtime;
		private $sourceEntity;
		private $sourceId;
		private $targetId;
		private $action;
		private $status;
		private $entityResStart;
		private $entityResEnd;
		private $entityShipStart;
		private $entityShipEnd;
		private $fleetResStart;
		private $fleetResEnd;
		private $fleetShipStart;
		private $fleetShipEnd;
		private $launched;
		private $fuel;
		private $food;
		private $pilots;
		private $text;
		
		private $severity;
		private $facility;
		
		public function FleetLog($userId=0,$sourceId, &$sourceEnt)
		{
			$this->userId=$userId;
			$this->sourceEntity = $sourceEnt;
			$this->sourceId=$sourceId;
			$this->status = 0;
			$this->severity = 0;
			$this->facility = 0;
			$this->launched=false;
			$this->entityResStart = $this->sourceEntity->getResourceLog();
			$this->fleetResStart = "0:0:0:0:0:0:0,f,0:0:0:0:0:0:0";
			$this->fleetShipStart = "0";
			
			$this->fleetId=0;
			$this->launchtime=0;
			$this->landtime=0;
			$this->targetId=0;
			$this->action="";
			$this->entityResEnd="";
			$this->entityShipStart="";
			$this->entityShipEnd="";
			$this->fleetResEnd="";
			$this->fleetShipEnd="";
			$this->fuel=0;
			$this->food=0;
			$this->pilots=0;
		}
		
		
		function __destruct()
		{
			if ($this->launched)
			{
				$this->text = "Treibstoff: ".$this->fuel." Nahrung: ".$this->food." Piloten: ".$this->pilots;
				dbquery("
						INSERT INTO 
							`logs_fleet` 
						(
						 	`fleet_id`,
							`facility`,
							`timestamp`,
							`message`,
							`user_id`,
							`entity_user_id`,
							`entity_from`,
							`entity_to`,
							`launchtime`,
							`landtime`,
							`action`,
							`status`,
							`fleet_res_start`,
							`fleet_res_end`,
							`fleet_ships_start`,
							`fleet_ships_end`,
							`entity_res_start`,
							`entity_res_end`,
							`entity_ships_start`,
							`entity_ships_end`
						) VALUES (
							'".$this->fleetId."',
							'".$this->facility."',
							'".time()."',
							'".$this->text."',
							'".$this->userId."',
							'".$this->userId."',
							'".$this->sourceId."',
							'".$this->targetId."',
							'".$this->launchtime."',
							'".$this->landtime."',
							'".$this->action."',
							'".$this->status."',
							'".$this->fleetResStart."',
							'".$this->fleetResEnd."',
							'".$this->fleetShipStart."',
							'".$this->fleetShipEnd."',
							'".$this->entityResStart."',
							'".$this->entityResEnd."',
							'".$this->entityShipStart."',
							'".$this->entityShipEnd."'
						);");
			}
		}		
		
		
		public function __set($key, $val)
		{
			try
			{
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);
				else
					$this->$key = $val;
					
			}
			catch (EException $e)
			{
				echo $e;
			}
		}
		
		public function __get($key)
		{
			try
			{
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in ".__CLASS__);
					
				return $this->$key;
			}
			catch (EException $e)
			{
				echo $e;
				return null;
			}
		}
		
		public function addFleetRes($res,$people,$fetch)
		{
			$this->fleetResEnd = "";
			
			foreach ($res as $rid=>$rcnt)
				if ($rid)
					$this->fleetResEnd .= $rcnt.":";
			$this->fleetResEnd .= $people.":0,f,";
			
			foreach ($fetch as $fid=>$fcnt)
				if ($fid)
					$this->fleetResEnd .= $fcnt.":";
		}
		
		public function launch()
		{
			$this->entityResEnd = $this->sourceEntity->getResourceLog();
			$this->facility = self::F_LAUNCH;
			$this->launched = true;
		}
		
			
	}

?>