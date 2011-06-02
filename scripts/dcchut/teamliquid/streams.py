# parse the TL streamers page
# and present the output in a nice format
import urllib, json
from lxml import html
from operator import attrgetter

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
  def __parseTLStreamsPage(self, url, featured_only):
    # lxml dies on some of the characters used, so kill them
    TL = urllib.urlopen(url).read()
    TL = TL.decode('UTF-8', 'ignore').encode('charmap', 'ignore')
    
    # only want FEATURED streams!
    if featured_only is True:
      TLfs    = TL.split('<h1>Live Streams</h1>')
      TLfs[1] = TLfs[1].split('</table>')[1:]
      TL      = TLfs[0] + '</table>'.join(TLfs[1])
    
    doc = html.document_fromstring(TL)
    return doc
  
  def __parseStreamers(self, doc):
    streamers = []
    
    # each TR (except the first) is a user
    for tr in doc.cssselect('#userstreams tr')[1:]:
      tr_user = ''
      tr_link = 'http://www.teamliquid.net'
      tr_viewers = ''
      tr_stage = 0
  
      for td in tr.cssselect('td'):
        if tr_user == '':

          # get the link hopefully
          for link in td.cssselect('a'):
            tr_link += link.get('href')
            tr_user = link.text_content()
            break

        else:
          if tr_stage == 2:
            tr_viewers = td.text_content()
            
            # get rid of the n/a bullshit
            if tr_viewers == 'n/a' or tr_viewers == '':
              tr_viewers = 0
        tr_stage += 1
        
      if tr_user == '':
        continue
      
      streamers.append(Stream(tr_user, tr_link, tr_viewers))
    return streamers
  
  def __init__(self, featured_only = None):
    self.streamers = []
    doc = self.__parseTLStreamsPage('http://www.teamliquid.net/video/streams/', featured_only)
    
    # parse the streamers & sort them by # of viewers, descending
    self.streamers = self.__parseStreamers(doc)
    self.streamers.sort(key=attrgetter('viewers'), reverse=True)
    
  def getJSON(self):
    return json.dumps(self.streamers, cls=Stream.Encoder)
