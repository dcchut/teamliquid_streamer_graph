# parse the TL streamers page
# and present the output in a nice format
import json
from lxml import etree
from operator import attrgetter
import urllib2
from StringIO import StringIO
from gzip import GzipFile

class Stream(object):
  class Encoder(json.JSONEncoder):
    def default(self, obj):
      if isinstance(obj, Stream):
       return [obj.user, obj.link, obj.viewers]
      return json.JSONEncoder.default(self, obj)
  
  def __init__(self, user, link, viewers):
    self.user = user
    self.link = link
    
    try:
      self.viewers = int(viewers)
    except ValueError:
      self.viewers = 0
      

class Streamers(object):
  def __parseStreamers(self, url, featured_only):
    streamers = []
    
    # get the stream data
    headers = {'User-Agent' : 'teamliquid featured streamers graph; http://pe.nitrated.net',
               'Accept-Encoding': 'gzip'}
       
    request  = urllib2.Request(url, headers=headers)
    response = urllib2.urlopen(request)
    
    buffer = StringIO(response.read())
    stream_data = GzipFile(fileobj=buffer).read()
    
    for e in etree.fromstring(stream_data).iter('stream'):
        # only get featured streams in this case
        if featured_only and int(e.get('featured')) != 1:
            continue
        
        # some people dont have a viewer count, ignore them
        if e.get('viewers') is None:
            continue

        link = None

        # multiple xml elements with the same name
        # seem to confuse lxml
        for l in e.xpath('link'):
            if l.get('type') == 'embed':
                link = l.text
                break
        
        streamers.append(Stream(e.get('owner'), link, e.get('viewers')))
    
    return streamers
    
  def __init__(self, featured_only = None):
    self.streamers = []
    
    # parse the streamers & sort them by # of viewers, descending
    self.streamers = self.__parseStreamers('http://www.teamliquid.net/video/streams/?filter=live&xml=1', featured_only)
    self.streamers.sort(key=attrgetter('viewers'), reverse=True)
    
  def getJSON(self):
    return json.dumps(self.streamers, cls=Stream.Encoder)
