YUI.add('moodle-block_course_level-courses', function(Y) {

    var COURSESNAME = 'blocks_course_courses';

    var COURSES = function() {
        COURSES.superclass.constructor.apply(this, arguments);
    };

    Y.extend(COURSES, Y.Base, {

        courserowevent:null,
        courserowvisible: [], //true if the courses are visible, else false
        courserows: [], //all the rows of courses

        initializer : function(params) {

            //attach a show event on the div with id = courses
            for (var i=0;i<this.get('courseids').length;i++)
            {
                var courseid = this.get('courseids')[i];
                var dlgContent = Y.one('#courseoverlay-'+courseid+' .contentBox').get('innerHTML');
                var dlgTitle = Y.one('#courseoverlay-'+courseid+' .dlgTitle').get('innerHTML');

                // Add a new hidden table row immediately under the current row
                this.courserows[courseid] = document.createElement("tr");
                this.courserows[courseid].setAttribute("id", "row-course-"+courseid);
                var cellEl = document.createElement("td");
                cellEl.setAttribute("colspan", "4");
                cellEl.innerHTML = dlgContent;
                this.courserows[courseid].appendChild(cellEl);
                this.courserows[courseid].style.display = "none";
                this.courserowvisible[courseid] = false;

                var referenceEl = document.getElementById("courseoverlay-"+courseid).parentNode.parentNode;
                this.insertAfter(referenceEl, this.courserows[courseid]);

                // Remove the dialog contents. If Javascript isn't enabled then this script won't run and the
                // list of courses will be left there.
                Y.one('#courseoverlay-'+courseid+' .contentBox').remove();

                // Replace the dialog title with a link. If Javascript isn't loaded then it will be left as a title.
                Y.one('#courseoverlay-'+courseid+' .dlgTitle').remove();

                var element = document.createElement("a");
                element.setAttribute("href", "#"); // Don't link anywhere.
                element.setAttribute("id", "courses-"+courseid); // Give it a unique id so we can attach a 'click' event to it.
                element.setAttribute("class", "dlgTitle");
                element.innerHTML = dlgTitle;
                Y.one('#courseoverlay-'+courseid).appendChild(element);

                // Specify the collapsed icon...
                var iconEl = document.createElement("img");
                iconEl.setAttribute("src", M.util.image_url("t/collapsed", "moodle"));
                iconEl.setAttribute("class", "courses-icon");
                Y.one('#courseoverlay-'+courseid).appendChild(iconEl);

                Y.one('#courseoverlay-'+courseid+' .dlgTitle').on('click', this.courseClick, this, courseid);
            }
        },

        insertAfter : function (referenceNode, newNode) {
            referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
        },

        courseClick : function (e, courseid) {

            if(this.courserowvisible[courseid]) {
                this.courserows[courseid].style.display = "none"; // hide the overlay
                this.courserowvisible[courseid] = false;

                // Change the icon:
                Y.one('#courseoverlay-'+courseid+' .courses-icon').remove();

                // Specify the collapsed icon...
                var iconEl = document.createElement("img");
                iconEl.setAttribute("src", M.util.image_url("t/collapsed", "moodle"));
                iconEl.setAttribute("class", "courses-icon");
                Y.one('#courseoverlay-'+courseid).appendChild(iconEl);

                // make heading not bold
                Y.one('#courseoverlay-'+courseid+' .dlgTitle').setStyle('font-weight', '');

            } else {
                this.courserows[courseid].style.display = ""; // show the overlay
                this.courserowvisible[courseid] = true;

                // Change the icon:
                Y.one('#courseoverlay-'+courseid+' .courses-icon').remove();

                // Specify the expanded icon...
                var iconEl = document.createElement("img");
                iconEl.setAttribute("src", M.util.image_url("t/expanded", "moodle"));
                iconEl.setAttribute("class", "courses-icon");
                Y.one('#courseoverlay-'+courseid).appendChild(iconEl);

                // make heading bold
                Y.one('#courseoverlay-'+courseid+' .dlgTitle').setStyle('font-weight', 'bold');
            }

            e.halt(); // we are going to attach a new 'hide overlay' event to the body,
            // because javascript always propagate event to parent tag,
            // we need to tell Yahoo to stop to call the event on parent tag
            // otherwise the hide event will be call right away.

            // we add a new event on the overlay in order to hide the overlay for the next click (touch device)
            this.courserowevent = Y.one("#courseoverlay-"+courseid+' .dlgTitle').on('click', this.hide, this, courseid);
        }

    }, {
        NAME : COURSESNAME,
        ATTRS : {
            courseids: {}
        }
    });

    M.blocks_course_level = M.blocks_course_level || {};
    M.blocks_course_level.init_courses = function(params) {
        return new COURSES(params);
    }

}, '@VERSION@', {
    requires:['base','overlay', 'moodle-enrol-notification']
});