
#ifndef __BUILDINGHANDLER__
#define __BUILDINGHANDLER__

#include <mysql++/mysql++.h>

#include "../EventHandler.h"

/**
* Handles building updates
* 
* \author Nicolas Perrenoud <mrcage@etoa.ch>
*/
namespace building
{
	class BuildingHandler	: EventHandler
	{
	public:
		BuildingHandler(mysqlpp::Connection* con)  : EventHandler(con) { this->changes_ = false; }
		void update();
		inline bool changes() { return this->changes_; }
		inline std::vector<int> getChangedPlanets() { return this->changedPlanets_; }
	private:
		bool changes_;
		std::vector<int> changedPlanets_;		
	};
}
#endif