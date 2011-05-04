#!/usr/bin/python
# output a list of streamers in JSON format
# by dcchut dcc.nitrated.net
from dcchut.teamliquid import streams

print streams.Streamers().getJSON()
