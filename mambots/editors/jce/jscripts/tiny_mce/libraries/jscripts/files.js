function setTree(dir){
	if(dir == '' || dir == '/'){
		parent.jce.tree.closeAll();
	}else{
		var divs = parent.document.getElementsByTagName('div');
		for(var i=0; i<divs.length; i++){
			if(divs[i].className == 'dTreeNode'){
				var child = divs[i].childNodes;
				for(var x=0; x<child.length; x++){
					if(child[x].title == dir && dir != ''){
						parent.jce.tree.closeAll();
						parent.jce.tree.openTo(parseInt(child[x].id.replace(/[^0-9]/g, '')), true);
					}									   
				}
			}				   
		}
	}
}
var Selectables = new Class({
	getOptions : function(){
		return {
			onSelect : Class.empty,
			multiple : true,
			accept: null
		};
	},
	initialize : function(element, options){
		this.setOptions(this.getOptions(), options);
		if (this.options.initialize) this.options.initialize.call(this);
		this.element = element;
		this.selected = new Array();
		
		$(element).addEvent('click', function(event){
			this.setSelected(event);
		}.bind(this));
	},
	setSelected : function(e){		
		e = new Event(e);
		var el = e.target;
		if(el.nodeName != 'DIV') el = el.parentNode;
		if(!this.isItem(el)) return;
		
		if(!e.control && !e.shift || !this.options.multiple){	
			if(this.isSelected(el) && this.selected.length == 1) return;			
			//deselect all
			this.selectNone();
			if(this.selected.length == 0){
				if(this.isSelected(el)){
					this.removeItem(el);
				}else{
					this.selectItem(el);
				}
			}
		// ctrl
		}else if(this.options.multiple && (e.control || e.shift)){
			if(this.isSelected(el)){
				this.removeItem(el);
			}else{
				this.selectItem(el);
			}
		}
	},
	selectNone : function(){
		this.selected.each(function(el){
			$(el).removeClass('selected');
		});
		this.selected = [];
	},
	selectItem : function(el){
		$(el).addClass('selected');
		this.selected.push(el);	
		this.fireEvent('onSelect', [this.selected]);
	},
	removeItem : function(el){
		if($type(el) == 'array'){
			el.each(function(element){
				if($(element)){
					$(element).removeClass('selected');
					this.selected.remove(element);	
				}
			}, this);
		}else{
			if($(el)){
				$(el).removeClass('selected');
				this.selected.remove(el);
			}
		}
		this.fireEvent('onSelect', [this.selected]);
	},
	isSelected : function(el){
		if(this.selected.test(el) && $(el).hasClass('selected')){
			return true;
		}
		return false;
	},
	getSelected : function(){
		return this.selected;	
	},
	isItem : function (node) {
		return node.parentNode == this.element;
	}
});
Selectables.implement(new Options);
Selectables.implement(new Events);

function setSelectables(){
	parent.jce.sf = null;
	parent.jce.sd = null;
	
	if($('fileList')){
		parent.jce.sf = new Selectables($('fileList'), {
			accept: ['divList', 'imgDiv'],
			multiple: true, 
			onSelect: function(elms){
				elms.each(function(el){
					if(elms.length > 1){
						parent.showNumFiles(elms);	
					}
					if(elms.length == 1){
						parent.showFileDetails(el.id);	
					}
					if(elms.length == 0){
						parent.resetManager();
					}
				});
				if(parent.jce.sd){
					parent.jce.sd.selectNone();
				}
			}
		});
	}
	if($('dirList')){
		parent.jce.sd = new Selectables($('dirList'), {
			accept: 'divList',
			multiple: false, 
			onSelect: function(elms){
				elms.each(function(el){
					if(el.id){
						parent.showFolderDetails(el.id);
					}
				});
				if(parent.jce.sf){
					parent.jce.sf.selectNone();	
				}
			}
		});
	}
};