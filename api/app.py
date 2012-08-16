import cherrypy

# ===========================================================================

class BioteaApi:
    exposed = True
    def __call__(self, Action=None, Selection=None):
        '''Main Runner'''

        #@TODO: Accept query paramaters as defined in the Biotea API Docs
        #@TODO: Figure out how to return errors in the user's desired representation

        #Determine the action
        if Action == None:
            out_obj = self.index()
        elif Action in dir(self):
            out_obj = getattr(self, Action)(Selection)
        else:
            raise cherrypy.HTTPError(404, "Not Found")

        #Temporary...
        return out_obj

        #What we really want to do...

        #Negotiate the request format (or 4XX Method not allowed)

        #Format the request output

        #Return the request output with the appropriate response header


    # -----------------------------------------------------------------------

    def index(self):
        return 'Index'

    # -----------------------------------------------------------------------

    def topics(self, Topic=None):
        return "Topics: {}".format(Topic) if Topic != None else "Topics here"
    
    # -----------------------------------------------------------------------
    
    def vocabularies(self, Vocabulary=None):
        return "Vocabularies: {}".format(Vocabulary) if Vocabulary != None else "Vocabularies here"
    
    # -----------------------------------------------------------------------
    
    def terms(self, Term=None):
       return "Terms: {}".format(Term) if Term != None else "Terms here"

    # -----------------------------------------------------------------------

    def negotiateResponse(self, overrideFormat=None):
        '''Negotiate the response based on the available output formatters'''
        availableFormats = ['application/json',
                            'application/xhtml+xml',
                            'text/html',
                            'application/json',
                            'application/xml',
                            'application/rdf+xml']

        return availableFormats
        mtype = cherrypy.tools.accept.callable(availableFormats, debug=True)
        return mtype

    # -----------------------------------------------------------------------


# ===========================================================================

if __name__ == '__main__':
    cherrypy.quickstart(BioteaApi())