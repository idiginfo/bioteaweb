
Biojs.Protein3DWS=Biojs.Protein3D.extend({constructor:function(options){this.base(options);if(this.opt.id!==undefined){this.requestPdb(this.opt.id);}},opt:{id:undefined,pdbUrl:'http://www.ebi.ac.uk/pdbe-srv/view/files',proxyUrl:'../biojs/dependencies/proxy/proxy.php'},eventTypes:["onRequestError"],requestPdb:function(pdbId){var self=this;self.showLoadingImage();self.opt.id=pdbId;jQuery.ajax({url:self.opt.proxyUrl,data:'url='+self.opt.pdbUrl+'/'+pdbId+'.pdb',dataType:'text',success:function(pdbContent){Biojs.console.log("DATA ARRIVED");self.setPdb(pdbContent);},error:function(qXHR,textStatus,errorThrown){self.raiseEvent('onRequestError',{message:textStatus});}});},getPdbId:function(pdb){return opt.id;}});