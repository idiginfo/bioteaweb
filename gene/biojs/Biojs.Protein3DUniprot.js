
Biojs.Protein3DUniprot=Biojs.Protein3DWS.extend({constructor:function(options){this.base(options);var self=this;this.onPdbLoaded(function(e){Biojs.console.log(e.result+" loading the pdb file "+e.file);Biojs.console.log("self._aligmentsJustArrived= "+self._alignmentsJustArrived);if(self._alignmentsJustArrived){Biojs.console.log("Initialising the alignments selection list");self.reset();var alignments=self._filterAligmentsBySelection(self._selection);var pdbOptions=self._createOptions(alignments);if(jQuery('#'+self.opt.target).find('div#pdbStructures').length==0){self._addControl('<div id="pdbStructures"></div>');}
jQuery('#'+self.opt.target).find('div#pdbStructures').html('<h1>Structures for <b>'+self.opt.proteinId+'</b></h1><br/>'+'<select id="pdbFile_select">'+pdbOptions+'</select>');jQuery('#'+self.opt.target+' #pdbFile_select').val(pdb);jQuery('#'+self.opt.target+' #pdbFile_select').change(function(){self._onAlignmentSelectionChange();});jQuery('#'+self.opt.target).find('#pdbStructures').show();var alignmentId=pdb;var pdbId=alignmentId.substring(0,pdb.indexOf('.')).toLowerCase();var alignment=self.getAlignmentsByPdb(alignmentId);if(alignment.hasOwnProperty(alignmentId)){var start=alignment[alignmentId][1].start;var end=alignment[alignmentId][1].end;self.raiseEvent('onPdbSelected',{"pdbId":pdbId,"alignmentId":alignmentId,"start":start,"end":end});}
self._alignmentsJustArrived=false;}});if(this.opt.proteinId!=undefined){var proteinId=this.opt.proteinId;this.opt.proteinId='';this.setProtein(proteinId);}},opt:{proteinId:undefined,alignmentsUrl:'http://www.rcsb.org/pdb/rest/das/pdb_uniprot_mapping/alignment?query='},eventTypes:["onPdbSelected"],_aligments:undefined,setProtein:function(proteinId){if(proteinId!=this.opt.proteinId){this.opt.proteinId=proteinId;this._selection=undefined;this._minStart=Number.MAX_VALUE;this._maxEnd=0;this._alignments=undefined;this._requestAligmentsXML();}},_requestAligmentsXML:function(){var self=this;self.showLoadingImage();jQuery.ajax({url:this.opt.proxyUrl,data:'url='+self.opt.alignmentsUrl+self.opt.proteinId,dataType:"text",success:function(xml){Biojs.console.log("SUCCESS: data received");self._parseResponse(xml);},async:false,error:function(qXHR,textStatus,errorThrown){Biojs.console.log("ERROR: requesting "+this.data);self.raiseEvent('onRequestError',{message:textStatus});}});},_parseResponse:function(xml){this._alignments={};var i=0;var self=this;Biojs.console.log("Decoding "+xml);try{xmlDoc=jQuery.parseXML(xml);jQuery(xmlDoc).find('block').each(function(){var children=jQuery(this).children();var segment0=self._createNode(children[0]);var segment1=self._createNode(children[1]);var arr=[];arr.push(segment0);arr.push(segment1);self._alignments[segment0.intObjectId]=arr||[];i++;if(self._minStart>segment1.start){self._minStart=segment1.start;}
if(self._maxEnd<segment1.end){self._maxEnd=segment1.end;}});}catch(e){Biojs.console.log("Error decoding response: "+e.message);this._alignments={};}
Biojs.console.log("Alignments decoded:");Biojs.console.log(self._alignments);this._aligmentsArrived();},_createNode:function(segment){var start=parseInt(jQuery(segment).attr('start'));var end=parseInt(jQuery(segment).attr('end'));var obj={intObjectId:jQuery(segment).attr('intObjectId')||'',start:start||'',end:end||''}
return obj;},_aligmentsArrived:function(){this._alignmentsJustArrived=true;var alignments=this._filterAligmentsBySelection(this._selection);if(!Biojs.Utils.isEmpty(alignments)){var pdb=undefined;for(pdb in alignments){break;}
Biojs.console.log("Requesting pdb "+pdb);this.requestPdb(pdb.substring(0,pdb.indexOf('.')).toLowerCase());}else{this._container.html("No structural information for "+this.opt.proteinId);this.raiseEvent('onRequestError',{message:"No structural information available for "+this.opt.proteinId});}},_onAlignmentSelectionChange:function(){var pdb=jQuery('#pdbFile_select').val();if(pdb!=undefined){var alignmentId=pdb.substring(0,pdb.indexOf(' '));var pdbId=alignmentId.substring(0,pdb.indexOf('.')).toLowerCase();var alignment=this.getAlignmentsByPdb(alignmentId);if(alignment.hasOwnProperty(alignmentId)){var start=alignment[alignmentId][1].start;var end=alignment[alignmentId][1].end;this.raiseEvent('onPdbSelected',{"pdbId":pdbId,"alignmentId":alignmentId,"start":start,"end":end});}
this.requestPdb(pdbId);}else{Biojs.console.log("No structural information available for "+this.opt.proteinId);}},getAlignmentsBySelection:function(selection){var alignments=this._alignments;if(selection!=undefined){this._filterAligmentsBySelection(selection);}
return alignments;},getAlignmentsByPdb:function(pdbId){var alignments={};for(al in this._alignments){if(this._alignments[al][0].intObjectId.indexOf(pdbId)!=-1){alignments[this._alignments[al][0].intObjectId]=this._alignments[al];}}
Biojs.console.log("Alignments for pdb "+pdbId);Biojs.console.log(alignments);return alignments;},filterAlignments:function(selection){var alignments=this._filterAligmentsBySelection(selection);var selectedAlignment=jQuery('#pdbFile_select').val();jQuery('#pdbFile_select').html(this._createOptions(alignments));if(alignments.hasOwnProperty(selectedAlignment.slice(0,selectedAlignment.indexOf(' ')))){jQuery('#pdbFile_select').val(selectedAlignment);}else{for(a in alignments){jQuery('#pdbFile_select').val(a);break;}
this._onAlignmentSelectionChange();}
this.base(selection);},_createOptions:function(alignments){var pdbOptions="";for(pdb in alignments){text=pdb+" ("+alignments[pdb][1].start+".."+alignments[pdb][1].end+")";pdbOptions+='<option value="'+text+'">'+text+'</option>';}
Biojs.console.log("_createOptions: "+pdbOptions);return pdbOptions;},_filterAligmentsBySelection:function(selection){var alignments=undefined;if(selection instanceof Array){alignments={};for(al in this._alignments){var uniprot=this._alignments[al][1];for(i in selection){if(selection[i]>=uniprot.start&&selection[i]<=uniprot.end){alignments[this._alignments[al][0].intObjectId]=this._alignments[al];break;}}}}else if(selection instanceof Object){alignments={};var i=0;for(al in this._alignments){var uniprot=this._alignments[al][1];if((selection.start>=uniprot.start&&selection.start<=uniprot.end)||(selection.end>=uniprot.start&&selection.end<=uniprot.end)||(selection.start<uniprot.start&&selection.end>uniprot.end)){alignments[this._alignments[al][0].intObjectId]=this._alignments[al];i++;}}}else{alignments=this._alignments;}
Biojs.console.log("Filtered alignments:");Biojs.console.log(alignments);return alignments;},setSelection:function(s){var selection=Biojs.Utils.clone(s);var alignmentId=this.getCurrentAlignmentId();var proteinId=this.getCurrentProteinId();var segment=this.getAlignmentsByPdb(alignmentId)[alignmentId];var offset=0;for(i in segment){if(segment[i].intObjectId==alignmentId){pdbSegment=segment[i];}else if(segment[i].intObjectId==proteinId){uniprotSegment=segment[i];}}
offset=uniprotSegment.start-pdbSegment.start;if(selection instanceof Array){for(i in selection){selection[i]-=offset;}}else if(selection instanceof Object&&selection.start<=selection.end){selection.start-=offset;selection.end-=offset;}
this.base(selection);},getCurrentAlignmentId:function(){var selectedValue=jQuery('#pdbFile_select').val();var alignmentId=selectedValue.substring(0,selectedValue.indexOf(' '));return alignmentId;},getCurrentProteinId:function(){return this.opt.proteinId;},removeSelection:function(){this.base();}});