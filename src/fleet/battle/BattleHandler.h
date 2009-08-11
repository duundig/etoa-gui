
#ifndef __BATTLEHANDLER__
#define __BATTLEHANDLER__

#include <ctime>
#include <cstdio>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../../MysqlHandler.h"
#include "../../config/ConfigHandler.h"
#include "../../util/Functions.h"

#include "../../objects/Fleet.h"
#include "../../entity/Entity.h"
#include "../../objects/Log.h"

#include "../../reports/BattleReport.h"

/**
* Handles battles....
*
* \author Stephan Vock <glaubinix@etoa.ch>
*/

class BattleHandler
{
	public:
		BattleHandler(Message* message) {
			this->message = new Message(message);
		}
		void battle(Fleet* fleet, Entity* entity, Log* log);

		~BattleHandler() {	}

		Message* message;

		bool alliancesHaveWar;

		short runde;

		double attPercent, defPercent;
		int attPoints,defPoints;
		short attResult, defResult;

		short returnV;
		std::string bstat, bstat2;
		bool returnFleet;

};
#endif
