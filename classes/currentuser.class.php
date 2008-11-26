<?PHP
	
	/**
	* Provides methods for accessing 
	* the current logged in user
	*
	* @author Nicolas Perrenoud<mrcage@etoa.ch>
	*/
	class CurrentUser extends User
	{
		protected $property;
		private $dmask;
		
		/**
		* Constructor which calls the default parent constructor
		* and loads settings
		*/
		public function CurrentUser($userId)
		{
			parent::User($userId);
		}

		//
		// Methods
		//		

		/**
		* Validates the user session against a given key
		*/
		public function validateSession($sessionKey)
		{
			$session_valid=false;
			if ($sessionKey!="")
			{
				// Valid browser values
				if (substr($sessionKey,64,32)==md5(ROUNDID) 
				&& substr($sessionKey,96,32)==md5($_SERVER['REMOTE_ADDR']) 
				&& substr($sessionKey,128,32)==md5($_SERVER['HTTP_USER_AGENT']) 
				&& substr($sessionKey,160)==session_id() )
				{
					// Valid user valies
					if ($this->lt=substr($sessionKey,0,32) && 
					$this->uid==substr($sessionKey,32,32) && 
					$this->sk==$sessionKey)
					{
						$session_valid=true;
					}
				}
			}
			return $session_valid;			
		}
		
		/**
		* Set setup status to false
		*/
		public function setNotSetup()
		{
			$this->setup = false;
		}
		
		function setSetupFinished()
		{
	    $sql = "
	    UPDATE
	    	users
	    SET
				user_setup=1
	    WHERE
	    	user_id='".$this->id."';";
	    dbquery($sql);
	    $this->setup=true;					
		}

		function loadDiscoveryMask()
		{
			$res = dbquery("
			SELECT
				discoverymask
			FROM				
				users
			WHERE
				user_id=".$this->id."
			");
			$this->dmask = '';
			$arr = mysql_fetch_row($res);
			if ($arr[0]=='')
			{
				for ($x=1;$x<=30;$x++)
				{
					for ($y=1;$y<=30;$y++)
					{
						$this->dmask.= '0';
					}
				}
			}
			else
			{
				$this->dmask=$arr[0];
			}			
		}

		function discovered($absX,$absY)
		{
			$cfg = Config::getInstance();
			$sy_num=$cfg->param2('num_of_sectors');
			$cy_num=$cfg->param2('num_of_cells');
			
			if (!isset($this->dmask))
			{
				$this->loadDiscoveryMask();
			}	
			
			$pos = $absX + ($cy_num*$sy_num)*($absY-1)-1;
			return ($this->dmask{$pos}%4);		
		}
		
		function setDiscovered($absX,$absY,$owner=1,$save=1)
		{
			$cfg = Config::getInstance();
			$sx_num=$cfg->param1('num_of_sectors');
			$cx_num=$cfg->param1('num_of_cells');
			$sy_num=$cfg->param2('num_of_sectors');
			$cy_num=$cfg->param2('num_of_cells');
			
			for ($x=$absX-1; $x<=$absX+1; $x++)
			{
				for ($y=$absY-1; $y<=$absY+1; $y++)
				{
					$pos = $x + ($cy_num*$sy_num)*($y-1)-1;
					if ($pos>= 0 && $pos <= $sx_num*$sy_num*$cx_num*$cy_num)
					{
						if ($owner==1)
						{
							$this->dmask{$pos} = '5';				
						}
						else
						{
							$this->dmask{$pos} = '1';
						}
					}
				}
			}	
			
			if ($save==1)
			{
				$this->saveDiscoveryMask();
			}			
		}	

		function saveDiscoveryMask()
		{
			dbquery("
			UPDATE
				users
			SET
				discoverymask='".$this->dmask."'
			WHERE
				user_id=".$this->id."
			");
		}
		
		function setPassword($oldPassword, $newPassword1, $newPassword2, &$returnMsg)
		{
			$res = dbquery("
			SELECT
				COUNT(user_id)
			FROM
				users
			WHERE
				user_password='".pw_salt($oldPassword,$this->registered)."'
				AND user_id=".$this->id.";");
			$arr = mysql_fetch_row($res);
			if ($arr[0]>0)
			{
				$res = dbquery("
				SELECT 
					COUNT(user_sitting_sitter_password) 
				FROM 
					user_sitting
				WHERE 
					user_sitting_sitter_password='".md5($_POST['user_password1'])."' 
					AND user_sitting_user_id=".$this->id.";");
				$arr = mysql_fetch_row($res);				
				if ($arr[0]==0)
				{
						if ($newPassword1==$newPassword2)
						{
								if (strlen($newPassword1)>=PASSWORD_MINLENGHT)
								{
										if (dbquery("
											UPDATE
											 	users
											SET
												user_password='".pw_salt($newPassword1,$this->registered)."'
											WHERE
												user_id='".$this->id."'
											;"))
										{
											add_log(3,"Der Spieler [b]".$this->nick."[/b] &auml;ndert sein Passwort!",time());
											send_mail("",$this->email,"Passwort�nderung","Hallo ".$this->nick."\n\nDies ist eine Best�tigung, dass du dein Passwort f�r deinen Account erfolgreich ge�ndert hast!\n\nSolltest du dein Passwort nicht selbst ge�ndet haben, so nimm bitte sobald wie m�glich Kontakt mit einem Game-Administrator auf: http://www.etoa.ch/?page=kontakt","","");
											$this->addToUserLog("settings","{nick} �ndert sein Passwort.",0);
											return true;
										}
								}
								else
								{
									$returnMsg = "Das Passwort muss mindestens ".PASSWORD_MINLENGHT." Zeichen lang sein!";
								}
						}
						else
						{
							$returnMsg="Die Eingaben m&uuml;ssen identisch sein!";
						}
				}
				else
				{
					$returnMsg="Das Passwort darf nicht identisch mit dem Sitterpasswort sein!";
				}
			}
			else
			{
				$returnMsg = "Dein altes Passwort stimmt nicht mit dem gespeicherten Passwort &uuml;berein!";
			}
			return false;
		}
	
	}  
	
	

?>