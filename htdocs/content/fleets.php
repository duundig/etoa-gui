<?PHP
	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	//
	
	/**
	* Shows information about current flights and incomming foreign fleets
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	//
	// Eigene Flotten
	//
	
	echo "<h1>Flotten</h1>";
	
	echo "<br/><input type=\"button\" onclick=\"document.location='?page=fleetstats'\" value=\"Schiffs&uuml;bersicht anzeigen\" /> &nbsp; ";
	
	//
	// Alliance fleets
	// 
	if (isset($_GET['mode']) && $_GET['mode']=="alliance" && $cu->allianceId>0) 
	{
		echo "<input type=\"button\" onclick=\"document.location='?page=fleets'\" value=\"Flotten anzeigen\" /><br/><br/>";
		
		if ($cu->allianceId()>0)
		{
			if ($cu->alliance->buildlist->getLevel(ALLIANCE_FLEET_CONTROL_ID)>=ALLIANCE_FLEET_SHOW)
			{
				$fm = new FleetManager($cu->id,$cu->allianceId);
				$fm->loadAllianceSupport();		
				
				if ($fm->count() > 0)
				{
					$cdarr = array();
					
					echo "Klicke auf den Auftrag um die Details einer Flotte anzuzeigen<br/><br/>";
					
					tableStart("Allianz Supportflotten");
					echo "<tr>
							<th>Auftrag</th>
							<th>Start / Ziel</th>
							<th>Start / Landung</th>
						</tr>";
					foreach ($fm->getAll() as $fid=>$fd)
					{
						$cdarr["cd".$fid] = $fd->landTime();
						
						echo "<tr>";
						echo "<td>";
						if ($cu->alliance->checkActionRightsNA('fleetminister'))
							echo "<a href=\"?page=fleetinfo&id=".$fid."\">";
							
						echo "<span style=\"font-weight:bold;color:".FleetAction::$attitudeColor[$fd->getAction()->attitude()]."\">
										".$fd->getAction()->name()."
									</span> [".FleetAction::$statusCode[$fd->status()]."]
								</a><br/>";
						if ($fd->landTime() < time())
						{
							if ($fd->status() > 0)
							{
								echo "Flotte landet...";
							}
							else
							{
								echo "Zielaktion wird durchgef&uuml;hrt...";
							}
						}
						else
						{
							echo "Ankunft in <b><span id=\"cd".$fid."\">-</span></b>";
						}
						echo "</td>";						
						echo "<td><b>".$fd->getSource()->entityCodeString()."</b> 
								<a href=\"?page=cell&amp;id=".$fd->getSource()->cellId()."&amp;hl=".$fd->getSource()->id()."\">".$fd->getSource()."</a><br/>
								<b>".$fd->getTarget()->entityCodeString()."</b> 
								<a href=\"?page=cell&amp;id=".$fd->getTarget()->cellId()."&amp;hl=".$fd->getTarget()->id()."\">".$fd->getTarget()."</a>
							</td>
							<td>".
								date("d.m.y, H:i:s",$fd->launchTime())."<br/>".
								date("d.m.y, H:i:s",$fd->landTime())."
							</td>";

						echo "</tr>";
					}
					tableEnd();
						
					foreach ($cdarr as $elem=>$t)
					{
						countDown($elem,$t);
					}		
				}
				else
				{
					iBoxStart("Allianz Supportflotten");
					echo "Es sind keine Allianz Supportflotten unterwegs!";
					iBoxEnd();
				}
				
				
				$fm->loadAllianceAttacks();		
				if ($fm->count() > 0)
				{
					$cdarr = array();
				
					echo "Klicke auf den Auftrag um die Details einer Flotte anzuzeigen<br/><br/>";
					
					tableStart("Allianz Angriffe");
					echo "<tr>
							<th>Start / Ziel</th>
							<th>Start / Landung</th>
							<th>Auftrag / Status</th>
						</tr>";
					foreach ($fm->getAll() as $fid=>$fd)
					{
						$cdarr["cd".$fid] = $fd->landTime();
						
						echo "<tr>
								<td><b>".$fd->getSource()->entityCodeString()."</b> 
									<a href=\"?page=cell&amp;id=".$fd->getSource()->cellId()."&amp;hl=".$fd->getSource()->id()."\">".$fd->getSource()."</a><br/>
									<b>".$fd->getTarget()->entityCodeString()."</b> 
									<a href=\"?page=cell&amp;id=".$fd->getTarget()->cellId()."&amp;hl=".$fd->getTarget()->id()."\">".$fd->getTarget()."</a>
								</td>
								<td>".
									date("d.m.y, H:i:s",$fd->launchTime())."<br/>".
									date("d.m.y, H:i:s",$fd->landTime())."
								</td>
								<td>";
						if ($cu->alliance->checkActionRightsNA('fleetminister'))
							echo "<a href=\"?page=fleetinfo&id=".$fid."&lead_id=".$fid."\">";
							
						echo "<span style=\"color:".FleetAction::$attitudeColor[$fd->getAction()->attitude()]."\">
											".$fd->getAction()->name()."
										</span> [".FleetAction::$statusCode[$fd->status()]."]
									</a><br/>";
						if ($fd->landTime() < time())
						{
							if ($fd->status() > 0)
							{
								echo "Flotte landet...";
							}
							else
							{
								echo "Zielaktion wird durchgef&uuml;hrt...";
							}
						}
						else
						{
							echo "Ankunft in <b><span id=\"cd".$fid."\">-</span></b>";
						}
						echo "</td></tr>";
					}
					tableEnd();
					
					foreach ($cdarr as $elem=>$t)
					{
						countDown($elem,$t);
					}		
				}
				else
				{
					iBoxStart("Allianz Angriffe");
					echo "Es sind keine Allianz Angriffe unterwegs!";
					iBoxEnd();
				}
			}
			else
			{
				error_msg("Allianzflottenkontrolle wurde noch nicht gebaut!");
			}
		}
		else
		{
			error_msg("Du gehörst noch keiner Allianz an.");
		}
	}	
	
	//
	// Personal fleets
	//
	else 
	{
		echo "<input type=\"button\" onclick=\"document.location='?page=fleets&mode=alliance'\" value=\"Allianzflotten anzeigen\" /><br/><br/>";
		
		$fm = new FleetManager($cu->id,$cu->allianceId);
		$fm->loadOwn();		
	
		if ($fm->count() > 0)
		{
			$cdarr = array();
			
			echo "Klicke auf den Auftrag um die Details einer Flotte anzuzeigen<br/><br/>";
			tableStart("Eigene Flotten");
			echo "
			<tr>
				<th>Auftrag</th>
				<th>Start / Ziel</th>
				<th>Start / Landung</th>
			</tr>";
			foreach ($fm->getAll() as $fid=>$fd)
			{
				$cdarr["cd".$fid] = $fd->landTime();
	
				echo "<tr>
				<td>
					<a href=\"?page=fleetinfo&id=".$fid."\">
					<span style=\"font-weight:bold;color:".FleetAction::$attitudeColor[$fd->getAction()->attitude()]."\">
					".$fd->getAction()->name()."
					</span> [".FleetAction::$statusCode[$fd->status()]."]</a><br/>";
				if ($fd->landTime() < time())
				{
					if ($fd->status() > 0)
					{
						echo "Flotte landet...";
					}
					else
					{
						echo "Zielaktion wird durchgef&uuml;hrt...";
					}
				}
				else
				{
					echo "Ankunft in <b><span id=\"cd".$fid."\">-</span></b>";
				}
				echo "</td>";
				echo "<td><b>".$fd->getSource()->entityCodeString()."</b> 
				<a href=\"?page=cell&amp;id=".$fd->getSource()->cellId()."&amp;hl=".$fd->getSource()->id()."\">".$fd->getSource()."</a><br/>";
				
				if ($cu->discovered($fd->getTarget()->getCell()->absX(),$fd->getTarget()->getCell()->absY()))
				{
					echo "<b>".$fd->getTarget()->entityCodeString()."</b> 
					<a href=\"?page=cell&amp;id=".$fd->getTarget()->cellId()."&amp;hl=".$fd->getTarget()->id()."\">".$fd->getTarget()."</a></td>";			
				}
				else
				{
					$ent = Entity::createFactory('u',$fd->getTarget()->id());
					echo "<b>".$ent->entityCodeString()."</b> 
					<a href=\"?page=cell&amp;id=".$ent->cellId()."&amp;hl=".$ent->id()."\">".$ent."</a></td>";			
				}
				echo "<td>
				".date("d.m.y, H:i:s",$fd->launchTime())."<br/>";
				echo date("d.m.y, H:i:s",$fd->landTime())."</td>";
				echo "</tr>";
			}
			tableEnd();
				
			foreach ($cdarr as $elem=>$t)
			{
				countDown($elem,$t);
			}		
		}
		else
		{
			iBoxStart("Eigene Flotten");
			echo "Es sind keine eigenen Flotten unterwegs!";
			iBoxEnd();
		}
	
	
		//
		// Gegnerische Flotten
		//
		$header=0;
		$fm->loadForeign();
		if ($fm->count() > 0)
		{
			tableStart("Fremde Flotten");
			foreach ($fm->getAll() as $fid=>$fd)
			{	
				// Is the attitude visible?
				if (SPY_TECH_SHOW_ATTITUDE<=$fm->spyTech())
				{
					$attitude = $fd->getAction()->attitude();
				}
				else
				{
					$attitude = 4;				
				}
				$attitudeColor = FleetAction::$attitudeColor[$attitude];
				$attitudeString = FleetAction::$attitudeString[$attitude];
				
				// Is the number of ships visible?
				if(SPY_TECH_SHOW_NUM<=$fm->spyTech())
				{
					$show_num = 1;
	
					//Zählt gefakte Schiffe wenn Aktion=Fakeangriff
					if($fd->getAction()->code()=="fakeattack")
					{
						$fsres = dbquery("
							SELECT 
								SUM(fs_ship_cnt) 
							FROM 
								fleet_ships
							WHERE 
								fs_fleet_id='".$fid." '
							GROUP BY 
								fs_fleet_id;");
						 $fsarr= mysql_fetch_row($fsres);
						$shipsCount = $fsarr[0];
					}
					else
					  $shipsCount = $fd->countShips();
				}
				else
				{
					$shipsCount = -1;
				}
				
				//Opfer sieht die einzelnen Schiffstypen in der Flotte
				$shipStr = array();
				if(SPY_TECH_SHOW_SHIPS<=$fm->spyTech())
				{
					$showShips = true;
					{

						$ships = array();

						//build new array with possible fake ships
						foreach ($fd->getShipIds() as $sid=> $scnt)
						{
							array_key_exists($fd->parseFake($sid), $ships) ? 
								$ships[$fd->parseFake($sid)] = $ships[$fd->parseFake($sid)] + $scnt :
								$ships[$fd->parseFake($sid)] = $scnt;
						}
						
						foreach ($ships as $sid=> $scnt)
						{
							$str = "";
							$ship = new Ship($sid);
							
							//Opfer sieht die genau Anzahl jedes Schifftypes in einer Flotte
							if (SPY_TECH_SHOW_NUMSHIPS<=$fm->spyTech())
							{
								$str= "".$scnt." ";
							}
								$str.= "".$ship->name();
								$shipStr[] = $str;
						}
					}
				}
	
				// Show action
				if (SPY_TECH_SHOW_ACTION<=$fm->spyTech())
				{
					$shipAction = $fd->getAction()->displayName();
				}
				else
				{
					$shipAction = $attitudeString;
				}
				
				if ($header!=1) 
				{
					echo "<tr>
						<th>Start / Ziel</th>
						<th>Startzeit / Landezeit</th>
						<th>Gesinnung</th>
						<th>Spieler</th>
						</tr>";
					$header=1;
				}
				
				echo "<tr>
					<td><b>".$fd->getSource()->entityCodeString()."</b> 
					<a href=\"?page=cell&amp;id=".$fd->getSource()->cellId()."&amp;hl=".$fd->getSource()->id()."\">".$fd->getSource()."</a><br/>";
				echo "<b>".$fd->getTarget()->entityCodeString()."</b> 
					<a href=\"?page=cell&amp;id=".$fd->getTarget()->cellId()."&amp;hl=".$fd->getTarget()->id()."\">".$fd->getTarget()."</a></td>";			
				echo "<td>
					".date("d.m.y, H:i:s",$fd->launchTime())."<br/>";
				echo date("d.m.y, H:i:s",$fd->landTime())."</td>";
				echo "<td>
					<span style=\"color:".$attitudeColor."\">
					".$shipAction."
					</span> [".FleetAction::$statusCode[$fd->status()]."]<br/>";				
				echo "<td>
					<a href=\"?page=messages&mode=new&message_user_to=".$fd->ownerId()."\">".get_user_nick($fd->ownerId())."</a>
					</td>";	
				echo "</tr>";
				if ($show_num==1)
				{
					echo "<tr><td colspan=\"4\">";
					echo "<b>Anzahl:</b> ".$shipsCount."";
					if ($showShips)
					{
						echo ";<br><b>Schiffe:</b> ";
						$count = false;
						foreach ($shipStr as $value) {
							if ($count) { 
								echo ", "; 
							} else {
								$count = true; 
							}
							echo $value;
						}
						/*if ($shipAction)
						{
							echo ";<br><b>Vorhaben:</b> ".$shipAction."";
						}*/
					}
					echo "</td></tr>";
				}
			}
			tableEnd();
		}
		else
		{
			iBoxStart("Fremde Flotten");
			echo "Es sind keine fremden Flotten zu deinen Planeten unterwegs!";
			iBoxEnd();
		}
	}



?>


