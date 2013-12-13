Orb.createNamespace('DeskPRO.Agent.ElementHandler');

DeskPRO.Agent.ElementHandler.SimpleAutoComplete = new Orb.Class({
	Extends: DeskPRO.ElementHandler,

	initPage: function() {
        var self = this;
        this.searchUrl = BASE_URL + 'agent/people-search/search-quick?format=json&limit=10&start_with=a';
        this.el.on('click', '.cc-people-search-trigger', function() {
            self.open();
        });
	},

    /**
     * Inits the search box to ensure it can be positioned properly
     */
    _initResultsBox: function() {
        var self = this;

        if (this._hasInitResultsBox) return;
        this._hasInitResultsBox = true;

        this.termInput   = $('.cc-people-search-trigger', this.el);
        this.resultsBox  = $('.person-search-box', this.el);
        this.resultsList = $('.results-list', this.resultsBox);
        this.addButton = $('.cc-saverow-trigger', this.el);

        this.tplHtml = DeskPRO_Window.util.getPlainTpl($('.user-row-tpl', this.el.parent()));

        //------------------------------
        // Update caller schedules the update requests
        //------------------------------

        var updateCaller =
        this.updateCaller = new DeskPRO.TouchCaller({
            timeout: 500,
            callback: (this.updateResults).bind(this),
            context: this
        });

        //------------------------------
        // Input events
        //------------------------------

        // Touch the timer so we will search in a few seconds,
        // or handle arrow and enter keys to select values in the list
        this.termInput.on('keypress', function(ev) {
            if (ev.keyCode == 27) {
                self.close.bind(self);
            } else if (ev.keyCode == 13 /* enter key */) {

                ev.preventDefault();

                var current = $('li.on', self.resultsList);

                if (current.length) {
                    var personId = current.data('person-id');
                    var name  = $('.user-name', current).text().trim();
                    var email = $('.user-email', current).text().trim();

                    self.termInput.val(email);
                    self.el.trigger('personsearchboxclick', [personId, name, email, self]);
                } else {
                    var term = self.getTerm();
                    self.el.trigger('personsearchboxclicknew', [term, self]);
                }

                self.addButton.click();
                self.close();
            } else if (ev.keyCode == 40 /* down key */ || ev.keyCode == 38 /* up key */) {

                ev.preventDefault();

                var dir = ev.keyCode == 40 ? 'down' : 'up';

                var current = $('li.on', self.resultsList);
                $('li', self.resultsList).removeClass('on');

                if (!current.length) {
                    if (dir == 'down') {
                        $('li', self.resultsList).first().addClass('on');
                    } else {
                        $('li', self.resultsList).last().addClass('on');
                    }
                } else {
                    if (dir == 'down') {
                        var next = current.next('li');
                        if (!next.length) {
                            next = $('li', self.resultsList).first();
                        }
                    } else {
                        var next = current.prev('li');
                        if (!next.length) {
                            next = $('li', self.resultsList).last();
                        }
                    }

                    next.addClass('on');
                }
            } else {
                updateCaller.touch(self.getTerm());
            }
        }).on('change', function() { updateCaller.touch(self.getTerm()); });

        // Stop bubbling so it doesnt reach the document and close itself
        this.termInput.on('click', function(ev) { self.open();ev.stopPropagation(); });
        this.resultsBox.on('click', function(ev) { ev.stopPropagation(); });

        $(document).on('click', this.close.bind(this));


        //------------------------------
        // Clicking on an item fires an event that
        // the page controller can listen to
        //------------------------------

        this.resultsList.on('click', 'li', function(ev) {
            ev.preventDefault();
            var email = $('.user-email', this).text().trim();

            self.termInput.val(email);
            self.addButton.click();
            self.close();
        });

        this.resultsBox.detach().hide().appendTo('body');
    },


    /**
     * Reset the box back to empty
     */
    reset: function() {
        if (this.runningAjax) {
            this.runningAjax.abort();
            this.runningAjax = null;
        }
        this.termInput.val('');
        this.resultsList.empty();
    },


    /**
     * Refresh the position of the search box relative to its bound element.
     */
    refreshPosition: function() {
        var termPos = this.termInput.offset();
        var termW   = this.termInput.outerWidth();
        var termH   = this.termInput.outerHeight();

        this.resultsBox.css({
            top: termPos.top + termH - 1,
            left: termPos.left,
            width: termW,
            position: 'absolute',
            zIndex: '1000'
        });
    },


    /**
     * Get the search term in the box
     *
     * @return {String}
     */
    getTerm: function() {
        return this.termInput.val().trim();
    },


    /**
     * Sends the ajax request to find users that match the term in the search box
     */
    updateResults: function() {
        var term = this.getTerm();

        var postData = [];
        postData.push({
            name: 'term',
            value: term
        });

        this.runningAjax = $.ajax({
            type: 'GET',
            url: this.searchUrl,
            data: postData,
            dataType: 'json',
            context: this,
            complete: function() {
                this.runningAjax = null;
            },
            success: function(data) {
                var currentPersonId = parseInt($('li.on', this.resultsList).data('person-id')) || 0;
                this.resultsList.empty();

                Array.each(data, function(user) {
                    var row = $(this.tplHtml);

                    row.data('person-id', user.id);
                    row.attr('person-id', user.id);
                    row.addClass('person-' + user.id);

                    if (currentPersonId && currentPersonId == parseInt(user.id)) {
                        row.addClass('on');
                        currentPersonId = false;
                    }

                    if (this.el.data('highlight-term')) {
                        var term  = Orb.escapeHtml(this.getTerm());
                        var name  = Orb.escapeHtml(user.name);
                        var email = Orb.escapeHtml(user.email);

                        term = (term+'').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1");
                        name = name.replace( new RegExp( "(" + term + ")", 'gi' ), '<span class="highlight">$1</span>' );
                        email = email.replace( new RegExp( "(" + term + ")", 'gi' ), '<span class="highlight">$1</span>' );

                        $('.user-name', row).html(name);
                        $('.user-email', row).html(email);

                    } else {
                        $('.user-name', row).text(user.name);
                        $('.user-email', row).text(user.email);
                    }

                    if (!user.email || user.name == user.email) {
                        $('address', row).hide();
                    }

                    this.resultsList.append(row);
                }, this);

                if(this.termInput.is(':focus') && data.length) {
                    this.open();
                }
            }
        });
    },


    /**
     * Opens the results box
     */
    open: function() {
        this._initResultsBox();

        this.refreshPosition();
        this.resultsBox.show();
    },


    /**
     * Closes the results box and stops any updating stuff
     */
    close: function() {
		// Could be destroyed so need to check that el still exists
		if (this.resultsBox) {
			this.resultsBox.hide();
		}
    },


    /**
     * Destroys the widget
     */
    destroy: function() {
        if (this._hasInitResultsBox) {
            this.resultsBox.remove();
        }

        this.resultsBox = null;
        this.resultsBox = null;
    }
});
