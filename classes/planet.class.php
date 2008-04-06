<?PHP
      
  /**
  * Planet class
  *
  * @author Nicolas Perrenoud <mrcage@etoa.ch>
  */
	class Planet extends Entity
	{
		protected $isValid;
		protected $coordsLoaded;
		
		/**
		* Constructor
		* Erwartet ein Array mit dem Inhalt des MySQL-Datensatzes, oder die ID eines Planeten
		*/
		function Planet($arr=null)
		{
			$this->isValid = false;
			
			if (!is_array($arr) && $arr>0)
			{
				$res = dbquery("
				SELECT
        	planets.*,
        	cells.sx,
        	cells.sy,
        	cells.cx,
        	cells.cy,
        	cells.id as cell_id,
        	entities.pos,
        	planet_types.*
				FROM 
				(
					planets
        	INNER JOIN 
          	planet_types 
            ON planets.planet_type_id = planet_types.type_id
            AND planets.id='".$arr."'
				)
        INNER JOIN 
        (	
        	entities
         	INNER JOIN cells 
          	ON cells.id = entities.cell_id
        )
        ON planets.id = entities.id
				;");

				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_assoc($res);
				}
				else
				{
					echo "Planet $arr nicht gefunden!\n";
				}
			}

			if ($arr)
			{				
				$this->id=$arr['id'];
				$this->cellId=$arr['cell_id'];
				$this->userId=$arr['planet_user_id'];
				$this->name= $arr['planet_name']!="" ? stripslashes($arr['planet_name']) : 'Unbenannt';
				$this->desc= stripslashes($arr['planet_desc']);
				$this->image=$arr['planet_image'];
				$this->updated=$arr['planet_last_updated'];
				$this->userChanged=$arr['planet_user_changed'];
				
				if ($arr['planet_user_id']>0)
				{
					$ures = dbquery("
					SELECT
						user_nick,
						user_race_id
					FROM
						users
					WHERE
						user_id=".$arr['planet_user_id']."
					");
					$uarr = mysql_Fetch_row($ures);
					$this->owner = $uarr[0];
					$this->ownerRaceId = $uarr[1];
				}
				else
				{
					$this->owner = "Niemand";	
					$this->ownerRaceId = 0;
				}
				
				
				
				$this->sx = $arr['sx'];
				$this->sy = $arr['sy'];
				$this->cx = $arr['cx'];
				$this->cy = $arr['cy'];
				$this->pos = $arr['pos'];

				$this->typeId = $arr['type_id'];
				$this->typeName = $arr['type_name'];

				$this->typeMetal=$arr['type_f_metal'];
				$this->typeCrystal=$arr['type_f_crystal'];
				$this->typePlastic=$arr['type_f_plastic'];
				$this->typeFuel=$arr['type_f_fuel'];
				$this->typeFood=$arr['type_f_food'];
				$this->typePower=$arr['type_f_power'];
				$this->typePopulation=$arr['type_f_population'];
				$this->typeResearchtime=$arr['type_f_researchtime'];
				$this->typeBuildtime=$arr['type_f_buildtime'];

				echo $this->typeBuildtime."#";

				$this->starTypeId=0;
				$this->starTypeName="Teststern";

				$this->starMetal=1;
				$this->starCrystal=1;
				$this->starPlastic=1;
				$this->starFuel=1;
				$this->starFood=1;
				$this->starPower=1;
				$this->starPopulation=1;
				$this->starResearchtime=1;
				$this->starBuildtime=1;

/*
				$this->starMetal=$arr['sol_type_f_metal'];
				$this->starCrystal=$arr['sol_type_f_crystal'];
				$this->starPlastic=$arr['sol_type_f_plastic'];
				$this->starFuel=$arr['sol_type_f_fuel'];
				$this->starFood=$arr['sol_type_f_food'];
				$this->starPower=$arr['sol_type_f_power'];
				$this->starPopulation=$arr['sol_type_f_population'];
				$this->starResearchtime=$arr['sol_type_f_researchtime'];
				$this->starBuildtime=$arr['sol_type_f_buildtime'];
*/
				$this->debrisMetal = $arr['planet_wf_metal'];
				$this->debrisCrystal = $arr['planet_wf_crystal'];
				$this->debrisPlastic = $arr['planet_wf_plastic'];

				$this->fields=$arr['planet_fields'];
				$this->fields_extra=$arr['planet_fields_extra'];
				$this->fields_used=$arr['planet_fields_used'];
				$this->temp_from=$arr['planet_temp_from'];
				$this->temp_to=$arr['planet_temp_to'];
				$this->people=zeroPlus($arr['planet_people']);
				$this->people_place=zeroPlus($arr['planet_people_place']);
				$this->res->metal=zeroPlus($arr['planet_res_metal']);
				$this->res->crystal=zeroPlus($arr['planet_res_crystal']);
				$this->res->plastic=zeroPlus($arr['planet_res_plastic']);
				$this->res->fuel=zeroPlus($arr['planet_res_fuel']);
				$this->res->food=zeroPlus($arr['planet_res_food']);
				$this->use->power=zeroPlus($arr['planet_use_power']);

				$this->resMetal=zeroPlus($arr['planet_res_metal']);
				$this->resCrystal=zeroPlus($arr['planet_res_crystal']);
				$this->resPlastic=zeroPlus($arr['planet_res_plastic']);
				$this->resFuel=zeroPlus($arr['planet_res_fuel']);
				$this->resFood=zeroPlus($arr['planet_res_food']);
				$this->usePower=zeroPlus($arr['planet_use_power']);


				$this->store->metal=$arr['planet_store_metal'];
				$this->store->crystal=$arr['planet_store_crystal'];
				$this->store->plastic=$arr['planet_store_plastic'];
				$this->store->fuel=$arr['planet_store_fuel'];
				$this->store->food=$arr['planet_store_food'];

				$this->prod->metal=$arr['planet_prod_metal'];
				$this->prod->crystal=$arr['planet_prod_crystal'];
				$this->prod->plastic=$arr['planet_prod_plastic'];
				$this->prod->fuel=$arr['planet_prod_fuel'];
				$this->prod->food=$arr['planet_prod_food'];
				$this->prod->power=zeroPlus($arr['planet_prod_power']);
				$this->prod->people=$arr['planet_prod_people'];

				$this->prodMetal=$arr['planet_prod_metal'];
				$this->prodCrystal=$arr['planet_prod_crystal'];
				$this->prodPlastic=$arr['planet_prod_plastic'];
				$this->prodFuel=$arr['planet_prod_fuel'];
				$this->prodFood=$arr['planet_prod_food'];
				$this->prodPower=zeroPlus($arr['planet_prod_power']);
				$this->prodPeople=$arr['planet_prod_people'];



				if ($arr['planet_user_main']==1)
					$this->isMain=true;
				else
					$this->isMain=false;

				$this->isValid = true;

			}
		}

		function id()
		{
			return $this->id;
		}

		function pos()
		{
			return $this->pos;
		}		

		function entityCode()
		{
			return "p";
		}

		function entityCodeString()
		{
			return "Planet";
		}

		function ownerId()
		{
			return $this->userId;
		}		

		function owner()
		{
			return $this->owner;
		}
		
		function type()
		{
			return $this->typeName;
		}		
		function imagePath($opt="")
		{
			if ($opt=="b")
			{
				return IMAGE_PATH."/planets/planet".$this->image.".".IMAGE_EXT;
			}
			if ($opt=="m")
			{
				return IMAGE_PATH."/planets/planet".$this->image."_middle.".IMAGE_EXT;
			}			
			return IMAGE_PATH."/planets/planet".$this->image."_small.".IMAGE_EXT;
		}		
		
		function name()
		{
			return $this->name;
		}

		function __toString()
		{
			return $this->formatedCoords()." ".$this->name;
		}
		
		function cellId()
		{
			return $this->cellId;
		}

		/**
		* Returns current cell and stellar system
		*
		* @return string
		*/
		function getSectorSolsys()
		{
			return $this->sx."/".$this->sy." : ".$this->cx."/".$this->cy;
		}
		
		function userChanged()
		{
			return $this->userChanged;
		}
		
		/**
		* Returns current coordinates
		*
		* @return string
		*/
		function getCoordinates()
		{
			return $this->formatedCoords();
		}		
		

		/** 
		* Displays a box with resources, power and population
		*/
		function resBox()
		{
			$style0="class=\"tbldata\"";
			$style1="class=\"tbldata\"";
			$style2="class=\"tbldata\"";
			$style3="class=\"tbldata\"";
			$style4="class=\"tbldata\"";
			$style5="class=\"tbldata\"";
			
			$store_msg=false;
			$power_msg=false;
			$place_msg=false;

			if ($this->store->metal<=floor($this->res->metal) && floor($this->res->metal)>0)
			{
				$style0="class=\"tbldata2\"";
				$store_msg=true;
			}
			if ($this->store->crystal<=floor($this->res->crystal) && floor($this->res->crystal)>0)
			{
				$style1="class=\"tbldata2\"";
				$store_msg=true;
			}
			if ($this->store->plastic<=floor($this->res->plastic) && floor($this->res->plastic)>0)
			{
				$style2="class=\"tbldata2\"";
				$store_msg=true;
			}
			if ($this->store->fuel<=floor($this->res->fuel) && floor($this->res->fuel)>0)
			{
				$style3="class=\"tbldata2\"";
				$store_msg=true;
			}
			if ($this->store->food<=floor($this->res->food) && floor($this->res->food)>0)
			{
				$style4="class=\"tbldata2\"";
				$store_msg=true;
			}
			if ($this->people_place<=floor($this->people) && floor($this->people)>0)
			{
				$style5="class=\"tbldata2\"";
				$place_msg=true;
			}
			if(floor($this->prod->power)-floor($this->use->power)<0)
			{
				$style6="class=\"tbldata2\"";
				$power_msg=true;
				$power_rest = floor($this->prod->power)-floor($this->use->power);
			}
			else
			{
				$style6="class=\"tbldata3\"";
				$power_rest = floor($this->prod->power)-floor($this->use->power);
			}
			infobox_start("Ressourcen",1);
			echo "<tr>
			<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_METAL." ".RES_METAL."</td>
			<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_CRYSTAL." ".RES_CRYSTAL."</td>
			<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_PLASTIC." ".RES_PLASTIC."</td>
			<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_FUEL." ".RES_FUEL."</td>
			<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_FOOD." ".RES_FOOD."</td>
			<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_PEOPLE." Bewohner</td>
			<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_POWER." Energie</td>
			</tr><tr>
			<td $style0>".nf(floor($this->res->metal))." t</td>
			<td $style1>".nf(floor($this->res->crystal))." t</td>
			<td $style2>".nf(floor($this->res->plastic))." t</td>
			<td $style3>".nf(floor($this->res->fuel))." t</td>
			<td $style4>".nf(floor($this->res->food))." t</td>
			<td $style5>".nf(floor($this->people))."</td>
			<td $style6>".nf($power_rest)."</td>
			</tr>";

			$text = array();
			if ($store_msg)
				array_push($text,"Speicher");
			if ($place_msg)
				array_push($text,"Wohnmodule");
			if ($power_msg)
				array_push($text,"Kraftwerke");


			if (count($text)>0)
			{
				echo "<tr><td class=\"tbldata\" colspan=\"7\" style=\"text-align:center;color:orange;\"><i>Es werden ben&ouml;tigt: ";

				$cnt=0;
                foreach ($text as $value)
                {
                	if($cnt!=0)
                		echo ", ";
                	echo "$value";
                	$cnt++;
                }
				echo "</i></td></tr>";
			}
			echo "</table><br/>";
		}

		/**
		* Saves updated content
		*/
		function save()
		{
			global $db_table;
      $sql = "
      UPDATE
      	planets
      SET
				planet_store_metal=".$this->store->metal.",
        planet_store_crystal=".$this->store->crystal.",
        planet_store_plastic=".$this->store->plastic.",
        planet_store_fuel=".$this->store->fuel.",
        planet_store_food=".$this->store->food.",
        planet_people_place=".$this->people_place.",
        planet_fields_used=".$this->fields_used.",
      	planet_fields_extra=".$this->fields_extra.",
        planet_prod_metal=".$this->prod->metal.",
        planet_prod_crystal=".$this->prod->crystal.",
        planet_prod_plastic=".$this->prod->plastic.",
        planet_prod_fuel=".$this->prod->fuel.",
        planet_prod_food=".$this->prod->food.",
        planet_prod_power=".$this->prod->power.",
        planet_prod_people=".$this->prod->people.",
        planet_use_power=".$this->use->power."      	
     	WHERE
         id='".$this->id."'
       ;";
       dbquery($sql);
		}

		/**
		* Meta-update-function for Buildings, Tech, Ships, Defense, 
		* and if required storage, fields and production rates
		*/
		function update($force=0)
		{
			global $db_table,$user,$conf;

			$u1 = $this->updateBuildings();
			$this->updateResearch();
			$u2 = $this->updateDefense();
			$u3 = $this->updateShips();			
			
			if ($u1 || $force==1)
			{
				$this->updateFields();
				$this->updateStorage();
				$this->updateProductionRates();
				$this->save();
			}

			if ($u2)
			{
				$this->updateFields();
				$this->save();
			}
			
			if ($u3)
			{
				$this->updateProductionRates();
				$this->save();
			}			
		}

		/**
		* Update resources
		*/
		function updateEconomy()
		{
    	global $db_table,$conf;

      $tmr = timerStart();
      $time = time();

      // Ressourcen und Bewohner updaten
      if ($this->updated==0)
      {
          $this->updated = $time;
      }
      $t = $time - $this->updated;

      if ($this->store->metal > $this->res->metal+($this->prod->metal/3600*$t))
          $this->res->metal+=$this->prod->metal/3600*$t;
      elseif ($this->store->metal > $this->res->metal)
          $this->res->metal=$this->store->metal;

      if ($this->store->crystal > $this->res->crystal+($this->prod->crystal/3600*$t))
          $this->res->crystal+=$this->prod->crystal/3600*$t;
      elseif ($this->store->crystal > $this->res->crystal)
          $this->res->crystal=$this->store->crystal;

      if ($this->store->plastic > $this->res->plastic+($this->prod->plastic/3600*$t))
          $this->res->plastic+=$this->prod->plastic/3600*$t;
      elseif ($this->store->plastic > $this->res->plastic)
          $this->res->plastic=$this->store->plastic;

      if ($this->store->fuel > $this->res->fuel+($this->prod->fuel/3600*$t))
          $this->res->fuel+=$this->prod->fuel/3600*$t;
      elseif ($this->store->fuel > $this->res->fuel)
          $this->res->fuel=$this->store->fuel;

      if ($this->store->food > $this->res->food+($this->prod->food/3600*$t))
          $this->res->food+=$this->prod->food/3600*$t;
      elseif ($this->store->food > $this->res->food)
          $this->res->food=$this->store->food;

      if ($this->store->food > $this->res->food+($this->prod->food/3600*$t))
          $this->res->food+=$this->prod->food/3600*$t;
      elseif ($this->store->food > $this->res->food)
          $this->res->food=$this->store->food;

      $birth_rate = $conf['people_multiply']['v'] + $this->type->population + $this->race->population + $this->sol->type->population -3;

      $people_births = $this->people/50 * ($birth_rate);
      if($people_births<=3)
      {
          $people_births=3;
      }
      if ($this->people==0 && $this->isMain)
      {
          $this->people=1;
      }
      if ($this->people_place > $this->people+($people_births/3600*$t))
      {
          $this->people+=$people_births/3600*$t;
      }
      elseif ($this->people_place>$this->people)
      {
          $this->people=$this->people_place;
			}

      $this->prod->people=$people_births;

      // Alles speichern
      dbquery("
      UPDATE
      	planets
      SET
          planet_res_metal='".$this->res->metal."',
          planet_res_crystal=".$this->res->crystal.",
          planet_res_plastic=".$this->res->plastic.",
          planet_res_fuel=".$this->res->fuel.",
          planet_res_food=".$this->res->food.",
          planet_people=".$this->people.",
          planet_prod_people=".$this->prod->people.",
          planet_last_updated='".$time."'
      WHERE
          id='".$this->id."';");

		}

		/**
		* Update storage values
		*/
		function updateStorage()
		{
    	global $db_table,$conf;

			$tmr=timerStart();

      // Basic store capacity
      $this->store->metal=intval($conf['def_store_capacity']['v']);
      $this->store->crystal=intval($conf['def_store_capacity']['v']);
      $this->store->plastic=intval($conf['def_store_capacity']['v']);
      $this->store->fuel=intval($conf['def_store_capacity']['v']);
      $this->store->food=intval($conf['def_store_capacity']['v']);
      $this->people_place=intval($conf['user_start_people']['p1']);

      // Storage capacity provided by buildings
      $bres = dbquery("
      SELECT
          buildings.building_people_place,
          buildings.building_store_metal,
          buildings.building_store_crystal,
          buildings.building_store_plastic,
          buildings.building_store_fuel,
          buildings.building_store_food,
          buildings.building_store_factor,

          buildlist.buildlist_current_level
      FROM
          	".$db_table['buildings']."
          INNER JOIN
          	".$db_table['buildlist']."
          ON buildings.building_id = buildlist.buildlist_building_id
          AND buildlist.buildlist_planet_id=".$this->id."
          AND buildlist.buildlist_current_level>0
          AND (buildings.building_store_metal>0
          OR buildings.building_store_crystal>0
          OR buildings.building_store_plastic>0
          OR buildings.building_store_fuel>0
          OR buildings.building_store_food>0
          OR buildings.building_people_place>0);");
      if (mysql_num_rows($bres)>0)
      {
          while ($barr=mysql_fetch_assoc($bres))
          {
              $level = $barr['buildlist_current_level']-1;
              $this->store->metal+=round($barr['building_store_metal'] * pow($barr['building_store_factor'],$level));
              $this->store->crystal+=round($barr['building_store_crystal'] * pow($barr['building_store_factor'],$level));
              $this->store->plastic+=round($barr['building_store_plastic'] * pow($barr['building_store_factor'],$level));
              $this->store->fuel+=round($barr['building_store_fuel'] * pow($barr['building_store_factor'],$level));
              $this->store->food+=round($barr['building_store_food'] * pow($barr['building_store_factor'],$level));
              $this->people_place+=floor($barr['building_people_place'] * pow($barr['building_store_factor'],$level));
          }
      }
      mysql_free_result($bres);
      unset($barr);

      //echo "Storages calculated in ".timerStop($tmr)."<br/>";	      		
		}

		/**
		* Update fields
		*/
		function updateFields()
		{
			global $db_table;

      $tmr=timerStart();
      
			$this->fields_used=0;
			$this->fields_extra=0;
			$bres = dbquery("
			SELECT
				SUM(buildings.building_fields*buildlist.buildlist_current_level) AS f,
				SUM(round(buildings.building_fieldsprovide * pow(buildings.building_production_factor,buildlist.buildlist_current_level-1))) AS e
			FROM
                ".$db_table['buildings'].",
                ".$db_table['buildlist']."
			WHERE
                buildlist.buildlist_building_id=buildings.building_id
                AND buildlist.buildlist_current_level>'0'
                AND buildlist.buildlist_planet_id=".$this->id.";");
			if (mysql_num_rows($bres)>0)
			{
				$barr=mysql_fetch_assoc($bres);
				$this->fields_used+=$barr['f'];
				$this->fields_extra+=$barr['e'];
			}
			$bres = dbquery("
			SELECT
				SUM(defense.def_fields*deflist.deflist_count) AS f
			FROM
				".$db_table['defense'].",
				".$db_table['deflist']."
			WHERE
				deflist.deflist_def_id=defense.def_id
				AND deflist.deflist_planet_id=".$this->id.";");
			if (mysql_num_rows($bres)>0)
			{
				$barr=mysql_fetch_assoc($bres);
				$this->fields_used+=$barr['f'];
			}
      //echo "Fields calculated in ".timerStop($tmr)."\n";
		}

		/**
		* Update production rates
		*/
		function updateProductionRates()
		{
    	global $db_table,$conf;

      $tmr=timerStart();

/*
			// Spezialisten-Boni laden
			$sres = dbquery("
			SELECT
				* 
			FROM
			
			WHERE
			;");
			*/

			$rated = false;

      // Produktionsraten berechnen
      $bres = dbquery("
      SELECT
          buildings.building_prod_metal,
          buildings.building_prod_crystal,
          buildings.building_prod_plastic,
          buildings.building_prod_fuel,
          buildings.building_prod_food,
          buildings.building_prod_power,
          buildings.building_production_factor,
          buildings.building_power_use,

          buildlist.buildlist_prod_percent,
          buildlist.buildlist_current_level
      FROM
          	".$db_table['buildings']."
          INNER JOIN
          	".$db_table['buildlist']."
          ON buildings.building_id = buildlist.buildlist_building_id
          AND buildlist.buildlist_planet_id=".$this->id."
          AND buildlist.buildlist_current_level>0
          AND (buildings.building_prod_metal>0
              OR buildings.building_prod_crystal>0
              OR buildings.building_prod_plastic>0
              OR buildings.building_prod_fuel>0
              OR buildings.building_prod_food>0
              OR buildings.building_prod_power>0
              OR buildings.building_power_use>0)                
      ORDER BY
          buildings.building_type_id,
          buildings.building_order;");
      if (mysql_num_rows($bres)>0)
      {
    	
          $cnt = array();
          $pwrcnt = 0;
          // Errechnen der Produktion pro GebÃ¤ude
          while ($barr = mysql_fetch_assoc($bres))
          {
              $cnt['metal'] += ceil($barr['building_prod_metal'] * $barr['buildlist_prod_percent'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1));
              $cnt['crystal'] += ceil($barr['building_prod_crystal'] * $barr['buildlist_prod_percent'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1));
              $cnt['plastic'] += ceil($barr['building_prod_plastic'] * $barr['buildlist_prod_percent'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1));
              $cnt['fuel'] += ceil($barr['building_prod_fuel'] * $barr['buildlist_prod_percent'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1));
              $cnt['food'] += ceil($barr['building_prod_food'] * $barr['buildlist_prod_percent'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1));
              $cnt['power'] += floor($barr['building_prod_power'] * $barr['buildlist_prod_percent'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1));
              $pwrcnt += $barr['building_power_use']*$barr['buildlist_prod_percent'] * pow($barr['building_production_factor'],$barr['buildlist_current_level']-1);
          }
          $rated=true;
			}

			$sres = dbquery("
			SELECT
				shiplist_count,
				ship_prod_power
			FROM
				shiplist
			INNER JOIN
				ships
				ON shiplist_ship_id=ship_id
				AND shiplist_planet_id=".$this->id."
				AND ship_prod_power>0
			");
			if (mysql_num_rows($sres)>0)
			{
				$dtemp = $this->solarPowerBonus();
				while ($sarr=mysql_fetch_assoc($sres))
				{					
					$cnt['power'] += ($sarr['ship_prod_power']+$dtemp) *$sarr['shiplist_count'];
				}
				$rated=true;
			}					
			
			if ($rated)
			{
          // Addieren der Planeten- und Rassenboni
          $cnt['metal']   += ($cnt['metal'] * ($this->type->metal + $this->race->metal + $this->sol->type->metal -3));
          $cnt['crystal'] += ($cnt['crystal'] * ($this->type->crystal +$this->race->crystal + $this->sol->type->crystal -3));
          $cnt['plastic'] += ($cnt['plastic'] * ($this->type->plastic + $this->race->plastic + $this->sol->type->plastic -3));
          $cnt['fuel']    += ($cnt['fuel'] * ($this->type->fuel + $this->race->fuel + $this->sol->type->fuel -3));
          $cnt['food']    += ($cnt['food'] * ($this->type->food + $this->race->food + $this->sol->type->food -3));
          $cnt['power']   += ($cnt['power'] * ($this->type->power + $this->race->power + $this->sol->type->power -3));

          // Bei ungenügend Energie Anpassung vornehmen
          if ($pwrcnt>=$cnt['power'])
          {
              $cnt['metal'] = floor($cnt['metal'] * $cnt['power'] / $pwrcnt);
              $cnt['crystal'] = floor($cnt['crystal'] * $cnt['power'] / $pwrcnt);
              $cnt['fuel'] = floor($cnt['fuel'] * $cnt['power'] / $pwrcnt);
              $cnt['plastic'] = floor($cnt['plastic'] * $cnt['power'] / $pwrcnt);
              $cnt['food'] = floor($cnt['food'] * $cnt['power'] / $pwrcnt);
          }

					// Berechnet noch Bewohnerproduktion (Dieser Wert ist nur für die Wirtschaftsübersicht zur Speicherberechnung)
		      $birth_rate = $conf['people_multiply']['v'] + $this->type->population + $this->race->population + $this->sol->type->population -3;

		      $people_births = $this->people/50 * ($birth_rate);
		      if($people_births<=3)
		      {
		          $people_births=3;
		      }

          $this->prod->metal=floor($cnt['metal']);
          $this->prod->crystal=floor($cnt['crystal']);
          $this->prod->plastic=floor($cnt['plastic']);
          $this->prod->fuel=floor($cnt['fuel']);
          $this->prod->food=floor($cnt['food']);
          $this->prod->power=floor($cnt['power']);
          $this->use->power=$pwrcnt;
          $this->prod->people=$people_births;
      }
      //echo "Production rate calculated in ".timerStop($tmr)."\n";
		}

		/**
		* Update buildings
		*
		* @return bool Did changes happen?
		*/
		function updateBuildings()
		{
			global $db_table;
			$afr=0;
			dbquery("
			UPDATE
				".$db_table['buildlist']."
			SET
                buildlist_current_level=buildlist_current_level+1,
                buildlist_build_type=0,
                buildlist_build_start_time=0,
                buildlist_build_end_time=0
			WHERE
				buildlist_planet_id=".$this->id."
                AND buildlist_build_type=1
                AND buildlist_build_end_time<".time()."
                AND buildlist_user_id='".$this->userId."';");
			$b = mysql_affected_rows();

			dbquery("
			UPDATE
				".$db_table['buildlist']."
			SET
                buildlist_current_level=buildlist_current_level-1,
                buildlist_build_type=0,
                buildlist_build_start_time=0,
                buildlist_build_end_time=0
			WHERE
				buildlist_planet_id=".$this->id."
                AND buildlist_build_type=2
                AND buildlist_build_end_time<".time()."
                AND buildlist_user_id='".$this->userId."';");

			$d = mysql_affected_rows();
			return ($d+$b>0) ? true : false;				
		}

		/**
		* Update technologies
		*/
		function updateResearch()
		{
			global $db_table;
			dbquery("
			UPDATE
			".$db_table['techlist']."
			SET
                techlist_current_level=techlist_current_level+1,
                techlist_build_type=0,
                techlist_build_start_time=0,
                techlist_build_end_time=0
			WHERE
                techlist_user_id='".$this->userId."'
                AND techlist_build_type=1
                AND techlist_build_end_time<".time().";");
			//echo mysql_affected_rows()." Technologien wurden aktualisiert!<br/>";
		}

		/**
		* Update ship build list
		*/
    function updateShips()
    {
      	global $db_table;
        $time = time();
        $changes = false;

      	$res=dbquery("
      	SELECT
      		queue_ship_id,
      		queue_user_id,
      		queue_endtime,
      		queue_objtime,
      		queue_cnt,
      		queue_id
      	FROM
      		".$db_table['ship_queue']."
      	WHERE
      		queue_planet_id='".$this->id."'
      		AND queue_starttime<".$time."
      	;");
      	if (mysql_num_rows($res)>0)
      	{
      		$empty=false;
      		while ($arr=mysql_fetch_assoc($res))
      		{
      			// Alle Schiffe als gebaut speichern da Endzeit bereits in der Vergangenheit
      			if ($arr['queue_endtime']<$time)
      			{
      				if ($arr['queue_cnt']>0)
      				{
      					shiplistAdd($this->id,$arr['queue_user_id'],$arr['queue_ship_id'],$arr['queue_cnt']);
      					$changes=true;
      				}
      				$empty=true;
      			}
      			// Bau ist noch im Gang
      			else
      			{
      				$obj_cnt=$arr['queue_cnt']-ceil(($arr['queue_endtime']-$time)/$arr['queue_objtime']);
      				shiplistAdd($this->id,$arr['queue_user_id'],$arr['queue_ship_id'],$obj_cnt);
      				$changes=true;
      				dbquery("
      				UPDATE
      					".$db_table['ship_queue']."
      				SET
      					queue_cnt=queue_cnt-".$obj_cnt."
      				WHERE
      					queue_id=".$arr['queue_id'].";
      				");
      			}
      		}
      		// Vergangene AuftrÃ¤ge lÃ¶schen
      		if ($empty)
      		{
          	dbquery("
          	DELETE FROM
          		".$db_table['ship_queue']."
          	WHERE
          		queue_planet_id='".$this->id."'
	        		AND queue_endtime<".$time."
          	;");
          }
      	}
      	return $changes;
    }

		/**
		* Update defense build list
		*
		* @return bool Did changes happen?
		*/
    function updateDefense()
    {
    	global $db_table;
      $time = time();
      $cnt = 0;

    	$res=dbquery("
    	SELECT
    		queue_def_id,
    		queue_user_id,
    		queue_endtime,
    		queue_objtime,
    		queue_cnt,
    		queue_id
    	FROM
    		".$db_table['def_queue']."
    	WHERE
    		queue_planet_id='".$this->id."'
    		AND queue_starttime<".$time."
    	;");
    	if (mysql_num_rows($res)>0)
    	{
    		$empty=false;
    		while ($arr=mysql_fetch_assoc($res))
    		{
    			// Alle Verteidigungsanlagen als gebaut speichern da Endzeit bereits in der Vergangenheit
    			if ($arr['queue_endtime']<$time)
    			{
    				if ($arr['queue_cnt']>0)
    				{
    					deflistAdd($this->id,$arr['queue_user_id'],$arr['queue_def_id'],$arr['queue_cnt']);
    					$cnt++;
    				}
    				$empty=true;
    			}
    			// Bau ist noch im Gang
    			else
    			{
    				$obj_cnt=$arr['queue_cnt']-ceil(($arr['queue_endtime']-$time)/$arr['queue_objtime']);
    				deflistAdd($this->id,$arr['queue_user_id'],$arr['queue_def_id'],$obj_cnt);
    				$cnt++;
    				dbquery("
    				UPDATE
    					".$db_table['def_queue']."
    				SET
    					queue_cnt=queue_cnt-".$obj_cnt."
    				WHERE
    					queue_id=".$arr['queue_id'].";
    				");
    			}
    		}
    		// Vergangene AuftrÃ¤ge lÃ¶schen
    		if ($empty)
    		{
        	dbquery("
        	DELETE FROM
        		".$db_table['def_queue']."
        	WHERE
        		queue_planet_id='".$this->id."'
        		AND queue_endtime<".$time."
        	;");
        }
    	}
      return $cnt>0 ? true : false;
    }

		/**
		* Changes resources on a planet
		*/
		function changeRes($m,$c,$p,$fu,$fo)
		{
		    global $db_table;
		    $sql = "
		    UPDATE
		    	planets
		    SET
                planet_res_metal=planet_res_metal+".$m.",
                planet_res_crystal=planet_res_crystal+".$c.",
                planet_res_plastic=planet_res_plastic+".$p.",
                planet_res_fuel=planet_res_fuel+".$fu.",
                planet_res_food=planet_res_food+".$fo."
		    WHERE
		    	id='".$this->id."';";
		    dbquery($sql);
		    $this->res->metal+=$m;
		    $this->res->crystal+=$c;
		    $this->res->plastic+=$p;
		    $this->res->fuel+=$fu;
		    $this->res->food+=$fo;
		}

		/**
		* Calculate bonus power production based on temperature
		*/
		function solarPowerBonus()
		{
			$v = floor(($this->temp_from + $this->temp_to)/4);
			if ($v <= -100)
			{
				$v = -99;
			}
			return $v;
		}

		/**
		* Calculate bonus power production based on temperature
		*/
		function fuelProductionBonus()
		{
			$v = floor(($this->temp_from + $this->temp_to)/25);
			return -$v;
		}

		/**
		* Calculate bonus power production based on temperature
		*/
		static function getSolarPowerBonus($t_min,$t_max)
		{
			$v = floor(($t_max + $t_min)/4);
			if ($v <= -100)
			{
				$v = -99;
			}
			return $v;
		}	
		
		function assignToUser($uid,$main=0)
		{
	    $sql = "
	    UPDATE
	    	planets
	    SET
				planet_user_id=".$uid.",
				planet_user_main=".$main."
	    WHERE
	    	id='".$this->id."';";
	    dbquery($sql);		
		}
		
		function setNameAndComment($name,$comment)
		{
			dbquery("
			UPDATE 
				planets 
			SET 
				planet_name='".$name."',
				planet_desc='".addslashes($comment)."' 
			WHERE 
				id='".$this->id."';");
			$this->name=$name;
			$this->desc=$comment;			
		}
	
		function setDefaultResources()
		{
			// Set default resources
			dbquery("
			UPDATE
				planets
			SET
	      planet_res_metal='".USR_START_METAL."',
	      planet_res_crystal='".USR_START_CRYSTAL."',
	      planet_res_plastic='".USR_START_PLASTIC."',
	      planet_res_fuel='".USR_START_FUEL."',
	      planet_res_food='".USR_START_FOOD."',
	      planet_people=".USR_START_PEOPLE."
			WHERE
				id=".$this->id().";");				
		}
		
		function reset()
		{
			dbquery("
				UPDATE
					planets
				SET
					planet_user_id=0,
					planet_name='',
					planet_user_main=0,
					planet_fields_used=0,
					planet_fields_extra=0,
					planet_res_metal=0,
					planet_res_crystal=0,
					planet_res_fuel=0,
					planet_res_plastic=0,
					planet_res_food=0,
					planet_use_power=0,
					planet_last_updated=0,
					planet_prod_metal=0,
					planet_prod_crystal=0,
					planet_prod_plastic=0,
					planet_prod_fuel=0,
					planet_prod_food=0,
					planet_prod_power=0,
					planet_store_metal=0,
					planet_store_crystal=0,
					planet_store_plastic=0,
					planet_store_fuel=0,
					planet_store_food=0,
					planet_people=1,
					planet_people_place=0,
					planet_desc=''
				WHERE
					id='".$this->id."';
			");
		}		
	
	}
?>