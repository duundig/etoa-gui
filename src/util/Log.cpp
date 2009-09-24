#include "Log.h"
#include "Debug.h"

void log(int priority, std::string message)
{
	if (debugEnable(0))
	{
		debugMsg(message);
	}
	openlog ("etoad", LOG_CONS | LOG_PID | LOG_NDELAY, LOG_LOCAL1);
	syslog (priority, message.c_str());
	closelog ();
}

void logPrio(int priority)
{
	setlogmask (LOG_UPTO (priority));
}
