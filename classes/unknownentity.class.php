<?PHP
	
	/**
	* Class for unknown space entities
	*/
	class UnknownEntity extends Entity
	{
		private $name;		
		protected $id;
		protected $coordsLoaded;
		protected $isValid;		
		protected $pos;
		protected $sx;
		protected $sy;
		protected $cx;
		protected $cy;
		protected $cellId;
		
		/**
		* The constructor
		*/
		function UnknownEntity($id=0)
		{
			$this->isValid = false;
			$this->id = $id;
			$this->pos = 0;
			$this->name = "Unbenannt";
			$this->coordsLoaded=false;
		}

		/**
		* Returns id
		*/                        
		function id() { return $this->id; }      

		/**
		* Returns id
		*/                        
		function name() { return $this->name; }      


		/**
		* Returns owner
		*/                        
		function owner() { return "Niemand"; }      

		/**
		* Returns owner
		*/                        
		function ownerId() { return 0; }      
	
		/**
		* Returns type string
		*/                        
		function entityCodeString() { return "Unbekannter Raum"; }      
	
		/**
		* Returns owner
		*/                        
		function pos() { return $this->pos; }      
		
		/**
		* Returns type
		*/
		function type()
		{
			return "Unbekannter Raum";
		}							

		function imagePath($opt="")
		{
			$r = mt_rand(1,10);
			return IMAGE_PATH."/space/space".$r."_small.".IMAGE_EXT;
		}

		/**
		* Returns type
		*/
		function entityCode() 
		{ 
			return "e"; 
		}	      
		
		/**
		* To-String function
		*/
		function __toString() 
		{
			if (!$this->coordsLoaded)
			{
				$this->loadCoords();
			}
			return $this->formatedCoords();
		}
		
		/**
		* Returns the cell id
		*/
		function cellId()
		{
			if (!$this->coordsLoaded)
			{
				$this->loadCoords();
			}
			return $this->cellId;
		}
		
	}
?>