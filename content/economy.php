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
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	File: economy.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Displays economy information
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	if ($cp)
	{
		//
		// Poduktionsrate umstellen
		//
		if (isset($_POST['submitpercent']) && $_POST['submitpercent']!="")
		{
			if (count($_POST['buildlist_prod_percent'])>0)
			{
				foreach ($_POST['buildlist_prod_percent'] as $id=>$val)
				{
					if ($val>1) $val=1; if ($val<0) $val=0;
					dbquery("
					UPDATE 
						buildlist 
					SET 
						buildlist_prod_percent=$val 
					WHERE 
						buildlist_user_id=".$cu->id()." 
						AND buildlist_entity_id=".$cp->id." 
						AND buildlist_building_id='$id'
					;");
				}
			}
		}

		echo "<h1>Wirtschaft des Planeten ".$cp->name."</h1>";
		$cp->resBox();

		if (SPECIALIST_MIN_POINTS_REQ <= $cu->points)
		{
			echo '<input type="button" onclick="document.location=\'?page=specialists\'" value="Spezialisten einstellen" /> ';
		}


		echo "<input type=\"button\" onclick=\"document.location='?page=planetstats'\" value=\"Ressourcen aller Planeten anzeigen\" />
		<br/><br/>";

		//
		// Produktion pro Stunde und Energieverbrauch
		//

		echo "<form action=\"?page=$page\" method=\"post\">";
		$bres = dbquery("
		SELECT 
			b.building_id,
			b.building_name,
			b.building_type_id,
			b.building_prod_metal,
			b.building_prod_crystal,
			b.building_prod_plastic,
			b.building_prod_fuel,
			b.building_prod_food,
			b.building_power_use,
			b.building_production_factor,
			l.buildlist_current_level,
			l.buildlist_prod_percent
		FROM 
      buildings AS b
		INNER JOIN
    	buildlist AS l
			ON	b.building_id=l.buildlist_building_id
	    AND l.buildlist_user_id=".$cu->id()."
	    AND l.buildlist_entity_id=".$cp->id()."
	    AND l.buildlist_current_level>0
	    AND (b.building_prod_metal>0
	        OR b.building_prod_crystal>0
	        OR b.building_prod_plastic>0
	        OR b.building_prod_fuel>0
	        OR b.building_prod_food>0
	        OR b.building_power_use>0)
		ORDER BY 
			b.building_type_id,
			b.building_order;");
		if (mysql_num_rows($bres)>0)
		{
			infobox_start("Produktion pro Stunde und Energieverbrauch",1);
			echo "<tr>
						<td style=\"\" class=\"tbltitle\">Geb&auml;ude</td>";
			echo "<td style=\"\" class=\"tbltitle\">".RES_ICON_METAL." ".RES_METAL."</td>";
			echo "<td style=\"\" class=\"tbltitle\">".RES_ICON_CRYSTAL."".RES_CRYSTAL."</td>";
			echo "<td style=\"\" class=\"tbltitle\">".RES_ICON_PLASTIC."".RES_PLASTIC."</td>";
			echo "<td style=\"\" class=\"tbltitle\">".RES_ICON_FUEL."".RES_FUEL."</td>";
			echo "<td style=\"\" class=\"tbltitle\">".RES_ICON_FOOD."".RES_FOOD."</td>";
			echo "<td style=\"\" class=\"tbltitle\" colspan=\"2\">".RES_ICON_POWER_USE."Energie</td>";
			echo "</tr>";

			$cnt = array();
			$pwrcnt = 0;
			while ($barr = mysql_fetch_array($bres))
			{
				// Ist das gebäudelevel > 0
				if($barr['buildlist_current_level']>0)
				{
					// Errechnen der Produktion pro Gebäude
          echo "<tr>
          	<td class=\"tbltitle\" style=\"width:170px;\">
          		".$barr['building_name']." (".$barr['buildlist_current_level'].")";
          if ($barr['buildlist_prod_percent']==0)
          {
          	echo "<br/><span style=\"color:red;font-size:8pt;\">Produktion ausgeschaltet!</span>";
          }          		
          echo "</td>";
          
          $bpb['metal'] = $bp['metal'] = $barr['building_prod_metal'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1);
          $bpb['crystal'] = $bp['crystal'] = $barr['building_prod_crystal'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1);
          $bpb['plastic'] = $bp['plastic'] = $barr['building_prod_plastic'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1);
          $bpb['fuel'] = $bp['fuel'] = $barr['building_prod_fuel'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1);
          $bpb['food'] = $bp['food'] = $barr['building_prod_food'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1);


          // Addieren der Planeten- und Rassenboni
          if ($bp['metal']!="")
          { 
          	$bp['metal']  = $bp['metal'] + ($bp['metal'] * ($cp->typeMetal-1)) + ($bp['metal'] * ($cu->raceMetal-1) + ($bp['metal'] * ($cp->starMetal-1)));
          }
          if ($bp['crystal']!="")
          { 
          	$bp['crystal']= $bp['crystal'] + ($bp['crystal'] * ($cp->typeCrystal-1)) +  ($bp['crystal'] * ($cu->raceCrystal-1) + ($bp['crystal'] * ($cp->starCrystal-1)));
          }
          if ($bp['plastic']!="")
          { 
          	$bp['plastic']= $bp['plastic'] + ($bp['plastic'] * ($cp->typePlastic-1)) + ($bp['plastic'] * ($cu->racePlastic-1) + ($bp['plastic'] * ($cp->starPlastic-1)));
          }
          if ($bp['fuel']!="")
          {
          	$bp['fuel'] = $bp['fuel'] + ($bp['fuel'] * ($cp->typeFuel-1)) + ($bp['fuel'] * ($cu->raceFuel-1) + ($bp['fuel'] * ($cp->starFuel-1)));
          }
          if ($bp['food']!="")
          {
          	$bp['food'] = $bp['food'] + ($bp['food'] * ($cp->typeFood-1)) + ($bp['food'] * ($cu->raceFood-1) + ($bp['food'] * ($cp->starFood-1)));
          }

          // Zum Total hinzufügen
          $cnt['metal'] += floor($bp['metal']*$barr['buildlist_prod_percent']);
          $cnt['crystal'] += floor($bp['crystal']*$barr['buildlist_prod_percent']);
          $cnt['plastic'] += floor($bp['plastic']*$barr['buildlist_prod_percent']);
          $cnt['fuel'] += floor($bp['fuel']*$barr['buildlist_prod_percent']);
          $cnt['food'] += floor($bp['food']*$barr['buildlist_prod_percent']);

          $building_power_use = floor($barr['building_power_use'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1));

          // Werte anzeigen
          echo "<td class=\"tbldata\" ".tm("Grundproduktion ohne Boni",nf(floor($bpb['metal']))." t/h").">".nf(floor($bp['metal']*$barr['buildlist_prod_percent']),1)."</td>";
          echo "<td class=\"tbldata\" ".tm("Grundproduktion ohne Boni",nf(floor($bpb['crystal']))." t/h").">".nf(floor($bp['crystal']*$barr['buildlist_prod_percent']),1)."</td>";
          echo "<td class=\"tbldata\" ".tm("Grundproduktion ohne Boni",nf(floor($bpb['plastic']))." t/h").">".nf(floor($bp['plastic']*$barr['buildlist_prod_percent']),1)."</td>";
          echo "<td class=\"tbldata\" ".tm("Grundproduktion ohne Boni",nf(floor($bpb['fuel']))." t/h").">".nf(floor($bp['fuel']*$barr['buildlist_prod_percent']),1)."</td>";
          echo "<td class=\"tbldata\" ".tm("Grundproduktion ohne Boni",nf(floor($bpb['food']))." t/h").">".nf(floor($bp['food']*$barr['buildlist_prod_percent']),1)."</td>";
          echo "<td class=\"tbldata\"";
          if ($building_power_use>0) 
          {
          	echo " style=\"color:#f00\"";
          }
          echo ">".nf(ceil($building_power_use*$barr['buildlist_prod_percent']))."</td>";
          echo "<td class=\"tbldata\">";
          if ($barr['building_type_id']==RES_BUILDING_CAT)
          {
              echo "<select name=\"buildlist_prod_percent[".$barr['building_id']."]\">\n";
              $prod_percent = $barr['buildlist_prod_percent'];
              for ($x=0;$x<1;$x+=0.1)
              {
                  if ($x>0.9) $vx=0;
                  else $vx = 1-$x;
                  $perc = $vx*100;
                  echo "<option value=\"".$vx."\"";
                  if (doubleval($vx)>=doubleval($prod_percent)) echo " selected=\"selected\"";
                  echo ">".$perc." %</option>\n";
              }
              echo "</select>";
          }
          elseif ($barr['building_id']==BUILD_MISSILE_ID || $barr['building_id']==BUILD_CRYPTO_ID)
          {
              echo "<select name=\"buildlist_prod_percent[".$barr['building_id']."]\">\n";
              echo "<option value=\"1\"";
              if ($barr['buildlist_prod_percent']==1)echo " selected=\"selected\"";
              echo ">100 %</option>\n";
              echo "<option value=\"0\"";
              if ($barr['buildlist_prod_percent']==0) echo " selected=\"selected\"";
              echo ">0 %</option>\n";
              echo "</select>";                    	
          }
          else
          {
              echo "&nbsp;";
          }
          echo "</td>";
          echo "</tr>";
          $pwrcnt += $building_power_use*$barr['buildlist_prod_percent'];
      	}
			}
			$pwrcnt=floor($pwrcnt);
			//echo "<tr><td class=\"tbltitle\" colspan=\"8\" height=\"4\"></td></tr>";

			// Anzeigen der Gesamtproduktion
			echo "<tr><td class=\"tbltitle\" style=\"height:2px;\" colspan=\"8\"></td></tr>";
			echo "<tr><td class=\"tbltitle\">TOTAL</td>";
			echo "<td class=\"tbldata\" style=\"color:#0f0\">".nf($cnt['metal'])."</td>";
			echo "<td class=\"tbldata\" style=\"color:#0f0\">".nf($cnt['crystal'])."</td>";
			echo "<td class=\"tbldata\" style=\"color:#0f0\">".nf($cnt['plastic'])."</td>";
			echo "<td class=\"tbldata\" style=\"color:#0f0\">".nf($cnt['fuel'])."</td>";
			echo "<td class=\"tbldata\" style=\"color:#0f0\">".nf($cnt['food'])."</td>";
			echo "<td class=\"tbldata\" style=\"color:#f00\">".nf($pwrcnt)."</td>";
			echo "<td class=\"tbldata\" style=\"color:#f00\"><input type=\"submit\" name=\"submitpercent\" class=\"button\" style=\"font-size:8pt;\" value=\"Speichern\" /></td>";
			echo "</tr>";

			$powerUsed = $pwrcnt;

			// Bei zuwenig Strom Warnmessage
			if ($pwrcnt > $cp->prodPower)
			{
				echo "<tr><td class=\"tbldata\" colspan=\"8\" style=\"color:#f00; text-align:center;\">Zuwenig Energie! ".nf(floor($pwrcnt))." ben&ouml;tigt, ".nf(floor($cp->prodPower))." verf&uuml;gbar. Gesamtproduktion wird auf ".(round($cp->prodPower / $pwrcnt,3)*100)."% gesenkt!</td></tr>";

				$cnt['metal'] = floor($cnt['metal'] * $cp->prodPower / $pwrcnt);
				$cnt['crystal'] = floor($cnt['crystal'] * $cp->prodPower / $pwrcnt);
				$cnt['fuel'] = floor($cnt['fuel'] * $cp->prodPower / $pwrcnt);
				$cnt['plastic'] = floor($cnt['plastic'] * $cp->prodPower / $pwrcnt);
				$cnt['food'] = floor($cnt['food'] * $cp->prodPower / $pwrcnt);

				echo "<tr><td class=\"tbltitle\">TOTAL</td>";
				echo "<td class=\"tbldata\">".nf($cnt['metal'])."</td>";
				echo "<td class=\"tbldata\">".nf($cnt['crystal'])."</td>";
				echo "<td class=\"tbldata\">".nf($cnt['plastic'])."</td>";
				echo "<td class=\"tbldata\">".nf($cnt['fuel'])."</td>";
				echo "<td class=\"tbldata\">".nf($cnt['food'])."</td>";
				echo "<td class=\"tbldata\" colspan=\"2\">".nf(floor($cp->prodPower))."</td>";
				echo "</tr>";
			}
			infobox_end(1);
		}
		else
		{
			echo "Es wurden noch keine Produktionsgeb&auml;ude gebaut!<br/><br/>";
		}
		echo "</form>";




		//
		// Energie
		//
		infobox_start("Energieproduktion",1);
		echo "<tr><th style=\"width:250px;\" class=\"tbltitle\">Gebäude</th>
		<th class=\"tbltitle\" colspan=\"2\">".RES_ICON_POWER."Energie</th></tr>";

		$cnt['power']=0;
		$pres = dbquery("
		SELECT 
			b.building_id,
			b.building_name,
			b.building_type_id,
			b.building_prod_power,
			b.building_power_use,
			b.building_production_factor,
			l.buildlist_current_level,
			l.buildlist_prod_percent
		FROM 
      ".$db_table['buildings']." AS b
      INNER JOIN
      buildlist AS l
      ON b.building_id=l.buildlist_building_id
      AND l.buildlist_user_id='".$cu->id()."'
      AND l.buildlist_entity_id='".$cp->id."'
      AND l.buildlist_current_level>'0'
      AND b.building_prod_power>'0'
		ORDER BY 
			b.building_order,
			b.building_name;");
		if (mysql_num_rows($pres)>0)
		{
			while ($parr = mysql_fetch_array($pres))
			{
				$bp['power'] 	= round($parr['building_prod_power'] * pow($parr['building_production_factor'],$parr['buildlist_current_level']-1));
				// Addieren der Planeten- und Rassenboni
				if ($bp['power']!="") $bp['power'] = $bp['power'] + ($bp['power'] * ($cp->typePower-1)) + ($bp['power'] * ($cu->racePower-1) + ($bp['power'] * ($cp->starPower-1)));

				echo "<tr><td class=\"tbltitle\">".$parr['building_name']." (".$parr['buildlist_current_level'].")</td>";
				echo "<td class=\"tbldata\" colspan=\"2\">".nf(floor($bp['power']))."</td></tr>";

				// Zum Total hinzufügen
				$cnt['power'] += $bp['power'];
			}
		}
	
		$power_bonus = ($cp->typePower + $cu->racePower + $cp->starPower-2);
	
		$sres = dbquery("
		SELECT
			shiplist_count,
			ship_prod_power,
			ship_name
		FROM
			shiplist
		INNER JOIN
			ships
			ON shiplist_ship_id=ship_id
			AND shiplist_entity_id=".$cp->id."
			AND shiplist_user_id=".$cu->id()."
			AND ship_prod_power>0
		");
		if (mysql_num_rows($sres)>0)
		{
			$dtemp = $cp->solarPowerBonus();
			if ($dtemp<0)
				$dtempstr = "<span style=\"color:#f00\">".$dtemp."</span>";
			else
				$dtempstr = "<span style=\"color:#0f0\">+".$dtemp."</span>";
			
			while ($sarr=mysql_fetch_array($sres))
			{
				$pwr = ($sarr['ship_prod_power']+ $dtemp) ;
				if ($pwr!="") 
					$pwr = $pwr * $power_bonus;
				$pwrt = $pwr * $sarr['shiplist_count'];
				echo '<tr><td class="tbltitle">'.$sarr['ship_name'].' ('.nf($sarr['shiplist_count']).')</td>';
				echo '<td colspan="2" class="tbldata">'.nf($pwrt).' 
				(Energie pro Satellit: '.(($pwr)).' = '.$sarr['ship_prod_power'].' Basis '.$dtempstr.' Solar '.get_percent_string($power_bonus,1).' Bonus)</td>';
				echo '</tr>';
				$cnt['power'] += $pwrt;
			}
		}		
					
		$powerProduced = $cnt['power']; 
		echo "<tr><td class=\"tbltitle\" style=\"height:2px;\" colspan=\"3\"></td></tr>";			
		echo "<tr><td class=\"tbltitle\">TOTAL produziert</td><td class=\"tbldata\" colspan=\"2\">".nf($powerProduced)."</td></tr>";
		if ($powerProduced!=0)
		{
			$powerFree = $powerProduced - $powerUsed;
			echo "<tr><td class=\"tbltitle\">Benutzt</td><td class=\"tbldata\"";
			echo ">".nf($powerUsed)."</td><td class=\"tbldata\">".round($powerUsed/$powerProduced*100,2)."%</td></tr>";
			if ($powerFree<0)
				$style=" style=\"color:#f00\"";
			else
				$style=" style=\"color:#0f0\"";
			echo "<tr><td class=\"tbltitle\">Verfügbar</td><td class=\"tbldata\" $style";
			echo ">".nf($powerFree)."</td><td class=\"tbldata\" $style>".round($powerFree/$powerProduced*100,2)."%</td></tr>";
		}
		echo "</table><br/><br/>";
		

		//
		// Lager
		//

		$bres = dbquery("
		SELECT
            b.building_name,
            b.building_store_metal,
            b.building_store_crystal,
            b.building_store_plastic,
            b.building_store_fuel,
            b.building_store_food,
            b.building_store_factor,
            l.buildlist_current_level
		FROM
            ".$db_table['buildings']." AS b,
            buildlist AS l
		WHERE
            b.building_id = l.buildlist_building_id
            AND l.buildlist_entity_id=".$cp->id."
            AND l.buildlist_current_level>0
            AND 
                (b.building_store_metal>0 
                OR b.building_store_crystal>0 
                OR b.building_store_plastic>0 
                OR b.building_store_fuel>0 
                OR b.building_store_food>0);");
		if (mysql_num_rows($bres)>0)
		{
			infobox_start("Lagerkapazit&auml;t",1);
			echo "<tr><td class=\"tbltitle\" style=\"width:160px\">Geb&auml;ude</td>";
			echo "<td class=\"tbltitle\">".RES_ICON_METAL."".RES_METAL."</td>";
			echo "<td class=\"tbltitle\">".RES_ICON_CRYSTAL."".RES_CRYSTAL."</td>";
			echo "<td class=\"tbltitle\">".RES_ICON_PLASTIC."".RES_PLASTIC."</td>";
			echo "<td class=\"tbltitle\">".RES_ICON_FUEL."".RES_FUEL."</td>";
			echo "<td class=\"tbltitle\">".RES_ICON_FOOD."".RES_FOOD."</td>";
			echo "</tr>";

			echo "<tr><td class=\"tbltitle\">Grundkapazit&auml;t</td>";
			for ($x=0;$x<5;$x++)
			{
  			echo "<td class=\"tbldata\">".nf($conf['def_store_capacity']['v'])."</td>";
  			$storetotal[$x]=$conf['def_store_capacity']['v'];
  		}
  		echo "</tr>";
			while ($barr=mysql_fetch_array($bres))
			{
					echo "<tr><td class=\"tbltitle\">".$barr['building_name']." (".$barr['buildlist_current_level'].")</td>";
					$level = $barr['buildlist_current_level']-1;
					$store[0]=round($barr['building_store_metal'] * pow($barr['building_store_factor'],$level));
					$store[1]=round($barr['building_store_crystal'] * pow($barr['building_store_factor'],$level));
					$store[2]=round($barr['building_store_plastic'] * pow($barr['building_store_factor'],$level));
					$store[3]=round($barr['building_store_fuel'] * pow($barr['building_store_factor'],$level));
					$store[4]=round($barr['building_store_food'] * pow($barr['building_store_factor'],$level));
					foreach ($store as $id=>$sd)
					{
						$storetotal[$id]+=$sd;
						echo "<td class=\"tbldata\">".nf($sd)."</td>";
					}
					echo "</tr>";
			}
			echo "<tr><td class=\"tbltitle\" style=\"height:2px;\" colspan=\"6\"></td></tr>";			
			echo "<tr><td class=\"tbltitle\">TOTAL</td>";
			foreach ($storetotal as $id=>$sd)
			{
				echo "<td class=\"tbldata\">".nf($sd,1)."</td>";
			}
			echo "</tr>";
			infobox_end(1);
		}


		//
		// Boni
		//

		infobox_start("Boni",1);


		echo "<tr><td class=\"tbltitle\">Rohstoff</td>
		<td class=\"tbltitle\">".$cp->typeName."</td>";
		echo "<td class=\"tbltitle\">".$cu->raceName."</td>";
		echo "<td class=\"tbltitle\">".$cp->starTypeName."</td>";
		echo "<td class=\"tbltitle\">TOTAL</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_METAL."Produktion ".RES_METAL."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->typeMetal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->raceMetal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->starMetal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($cp->typeMetal,$cu->raceMetal,$cp->starMetal),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_CRYSTAL."Produktion ".RES_CRYSTAL."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->typeCrystal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->raceCrystal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->starCrystal,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($cp->typeCrystal,$cu->raceCrystal,$cp->starCrystal),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_PLASTIC."Produktion ".RES_PLASTIC."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->typePlastic,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->racePlastic,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->starPlastic,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($cp->typePlastic,$cu->racePlastic,$cp->starPlastic),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_FUEL."Produktion ".RES_FUEL."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->typeFuel,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->raceFuel,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->starFuel,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($cp->typeFuel,$cu->raceFuel,$cp->starFuel),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_FOOD."Produktion ".RES_FOOD."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->typeFood,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->raceFood,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->starFood,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($cp->typeFood,$cu->raceFood,$cp->starFood),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_POWER."Produktion Energie</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->typePower,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->racePower,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->starPower,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($cp->typePower,$cu->racePower,$cp->starPower),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_PEOPLE."Bev&ouml;lkerungswachstum</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->typePopulation,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->racePopulation,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->starPopulation,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($cp->typePopulation,$cu->racePopulation,$cp->starPopulation),1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_TIME."Forschungszeit</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->typeResearchtime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->raceResearchtime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->starResearchtime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($cp->typeResearchtime,$cu->raceResearchtime,$cp->starResearchtime),1,1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_TIME."Bauzeit (Geb&auml;ude)</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->typeBuildtime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->raceBuildtime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cp->starBuildtime,1,1)."</td>";
		echo "<td class=\"tbldata\">".get_percent_string(array($cp->typeBuildtime,$cu->raceBuildtime,$cp->starBuildtime),1,1)."</td></tr>";

		echo "<tr><td class=\"tbldata\">".RES_ICON_TIME."Flugzeit</td>";
		echo "<td class=\"tbldata\">-</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->raceFleettime,1,1)."</td>";
		echo "<td class=\"tbldata\">-</td>";
		echo "<td class=\"tbldata\">".get_percent_string($cu->raceFleettime,1,1)."</td></tr>";

		infobox_end(1);

	}
	else
		echo "<h2>Fehler</h2> Dieser Planet existiert nicht oder er geh&ouml;rt nicht dir!";

?>

