YUI.add('moodle-block_course_level-units', function(Y) {

    var UNITSNAME = 'blocks_course_units';

    var UNITS = function() {
        UNITS.superclass.constructor.apply(this, arguments);
    };

    Y.extend(UNITS, Y.Base, {

        event:null,
        unitrowevent:null,
        unitrowvisible: [], //true if the units are visible, else false
        unitrows: [], //all the rows of units

        initializer : function(params) {

            //attach a show event on the div with id = units
            for (var i=0;i<this.get('unitids').length;i++)
            {
                var unitid = this.get('unitids')[i];
                var dlgContent = Y.one('#unitoverlay-'+unitid+' .contentBox').get('innerHTML');
                var dlgTitle = Y.one('#unitoverlay-'+unitid+' .dlgTitle').get('innerHTML');

                // Add a new hidden table row immediately under the current row
                this.unitrows[unitid] = document.createElement("tr");
                this.unitrows[unitid].setAttribute("id", "row-units-"+unitid);
                var cellEl = document.createElement("td");
                cellEl.setAttribute("colspan", "4");
                cellEl.innerHTML = dlgContent;
                this.unitrows[unitid].appendChild(cellEl);
                this.unitrows[unitid].style.display = "none";
                this.unitrowvisible[unitid] = false;

                var referenceEl = document.getElementById("unitoverlay-"+unitid).parentNode.parentNode;
                this.insertAfter(referenceEl, this.unitrows[unitid]);

                // Remove the dialog contents. If Javascript isn't enabled then this script won't run and the
                // list of units will be left there.
                Y.one('#unitoverlay-'+unitid+' .contentBox').remove();

                // Replace the dialog title with a link. If Javascript isn't loaded then it will be left as a title.
                Y.one('#unitoverlay-'+unitid+' .dlgTitle').remove();

                var element = document.createElement("a");
                element.setAttribute("href", "#"); // Don't link anywhere.
                element.setAttribute("id", "units-"+unitid); // Give it a unique id so we can attach a 'click' event to it.
                element.setAttribute("class", "dlgTitle");
                element.innerHTML = dlgTitle;
                Y.one('#unitoverlay-'+unitid).appendChild(element);

                // Specify the collapsed icon...
                var iconEl = document.createElement("img");
                iconEl.setAttribute("src", M.util.image_url("t/collapsed", "moodle"));
                iconEl.setAttribute("class", "units-icon");
                Y.one('#unitoverlay-'+unitid).appendChild(iconEl);

                Y.one('#unitoverlay-'+unitid+' .dlgTitle').on('click', this.unitClick, this, unitid);
            }

        },

        insertAfter : function (referenceNode, newNode) {
            referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
        },

        unitClick : function (e, unitid) {

            if(this.unitrowvisible[unitid]) {
                this.unitrows[unitid].style.display = "none"; // hide the overlay
                this.unitrowvisible[unitid] = false;

                // Change the icon:
                Y.one('#unitoverlay-'+unitid+' .units-icon').remove();

                // Specify the collapsed icon...
                var iconEl = document.createElement("img");
                iconEl.setAttribute("src", M.util.image_url("t/collapsed", "moodle"));
                iconEl.setAttribute("class", "units-icon");
                Y.one('#unitoverlay-'+unitid).appendChild(iconEl);

                // make heading not bold
                Y.one('#unitoverlay-'+unitid+' .dlgTitle').setStyle('font-weight', '');

            } else {
                this.unitrows[unitid].style.display = ""; // show the overlay
                this.unitrowvisible[unitid] = true;

                // Change the icon:
                Y.one('#unitoverlay-'+unitid+' .units-icon').remove();

                // Specify the expanded icon...
                var iconEl = document.createElement("img");
                iconEl.setAttribute("src", M.util.image_url("t/expanded", "moodle"));
                iconEl.setAttribute("class", "units-icon");
                Y.one('#unitoverlay-'+unitid).appendChild(iconEl);

                // make heading bold
                Y.one('#unitoverlay-'+unitid+' .dlgTitle').setStyle('font-weight', 'bold');
            }

            e.halt(); // we are going to attach a new 'hide overlay' event to the body,
            // because javascript always propagate event to parent tag,
            // we need to tell Yahoo to stop to call the event on parent tag
            // otherwise the hide event will be call right away.

            // we add a new event on the overlay in order to hide the overlay for the next click (touch device)
            this.unitrowevent = Y.one("#unitoverlay-"+unitid+' .dlgTitle').on('click', this.hide, this, unitid);
        }

    }, {
        NAME : UNITSNAME,
        ATTRS : {
            unitids: {}
        }
    });

    M.blocks_course_level = M.blocks_course_level || {};
    M.blocks_course_level.init_units = function(params) {
        return new UNITS(params);
    }

}, '@VERSION@', {
    requires:['base','overlay', 'moodle-enrol-notification']
});