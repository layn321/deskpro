Orb.createNamespace('DeskPRO');

/**
 * Finds words in text and wraps them in a span
 */
DeskPRO.WordHighlighter = {
	highlight: function(node, words, excluseStopwords, onlyFirst) {

		var i, w;

		// We need the longest words to process first or they'll be passed up in favour of shorter guys
		words.sort(function(a, b) {
			if (a.length > b.length) {
				return -1;
			} else {
				return 1;
			}
		});

		if (excluseStopwords) {
			words = words.filter(function(w) {
				return (DeskPRO.WordHighlighter.stopWords.indexOf(w) === -1);
			});
		}

		if (!words.length) {
			return [];
		}

		// Build a list of words we know are actually in the text
		var text = $(node).text().toLowerCase();
		var useWords = [];
		for (i = 0; i < words.length; i++) {
			var w = words[i].toLowerCase();
			if (!w || !w.length) {
				continue;
			}
			if (text.indexOf(w) !== -1) {
				useWords.push(w);
			}
		}

		if (!useWords.length) {
			return [];
		}

		var addedNodes = [];
		this._do(node, useWords, words, addedNodes, onlyFirst, {});

		return addedNodes;
	},

	_do: function(node, words, originalWords, addedNodes, onlyFirst, _doneWords) {
		var i, tmp;

		var proc_node = [node];
		var replaceBits = [];

		while (node = proc_node.pop()) {
			if (node.nodeType == 3) {
				for (i = 0; i < words.length; i++) {
					if (onlyFirst && _doneWords[i]) continue;

					var pos = node.data.toLowerCase().indexOf(words[i]);
					if (pos >= 0 && !$(node.parentNode).hasClass('dp-highlight-word') && !$(node.parentNode).closest('.dp-highlight-word')[0]) {
						_doneWords[i] = true;

						var spannode = document.createElement('span');
						spannode.className = 'dp-highlight-word';
						spannode.setAttribute('data-word', originalWords[i]);
						addedNodes.push(spannode);

						var middlebit = node.splitText(pos);
						var endbit = middlebit.splitText(words[i].length);
						var middleclone = middlebit.cloneNode(true);
						spannode.appendChild(middleclone);

						middlebit.parentNode.replaceChild(spannode, middlebit);

						proc_node.push(endbit);
					}
				}
			} else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
				var children = $.makeArray(node.childNodes);
				for (i = 0; i < children.length; i++) {
					proc_node.push(children[i]);
				}
			}
		}
	},

	/**
	 * Engish stop words courtesy of MySQL
	 */
	stopWords: [
		"a's","able","about","above","according","accordingly","across","actually","after","afterwards","again",
		"against","ain't","all","allow","allows","almost","alone","along","already","also","although","always",
		"am","among","amongst","an","and","another","any","anybody","anyhow","anyone","anything","anyway","anyways",
		"anywhere","apart","appear","appreciate","appropriate","are","aren't","around","as","aside","ask","asking",
		"associated","at","available","away","awfully","be","became","because","become","becomes","becoming","been",
		"before","beforehand","behind","being","believe","below","beside","besides","best","better","between","beyond",
		"both","brief","but","by","c'mon","c's","came","can","can't","cannot","cant","cause","causes","certain",
		"certainly","changes","clearly","co","com","come","comes","concerning","consequently","consider","considering",
		"contain","containing","contains","corresponding","could","couldn't","course","currently","definitely",
		"described","despite","did","didn't","different","do","does","doesn't","doing","don't","done","down",
		"downwards","during","each","edu","eg","eight","either","else","elsewhere","enough","entirely","especially",
		"et","etc","even","ever","every","everybody","everyone","everything","everywhere","ex","exactly","example",
		"except","far","few","fifth","first","five","followed","following","follows","for","former","formerly","forth",
		"four","from","further","furthermore","get","gets","getting","given","gives","go","goes","going","gone","got",
		"gotten","greetings","had","hadn't","happens","hardly","has","hasn't","have","haven't","having","he","he's",
		"hello","help","hence","her","here","here's","hereafter","hereby","herein","hereupon","hers","herself","hi",
		"him","himself","his","hither","hopefully","how","howbeit","however","i'd","i'll","i'm","i've","ie","if",
		"ignored","immediate","in","inasmuch","inc","indeed","indicate","indicated","indicates","inner","insofar",
		"instead","into","inward","is","isn't","it","it'd","it'll","it's","its","itself","just","keep","keeps",
		"kept","know","knows","known","last","lately","later","latter","latterly","least","less","lest","let","let's",
		"like","liked","likely","little","look","looking","looks","ltd","mainly","many","may","maybe","me","mean",
		"meanwhile","merely","might","more","moreover","most","mostly","much","must","my","myself","name","namely",
		"nd","near","nearly","necessary","need","needs","neither","never","nevertheless","new","next","nine","no",
		"nobody","non","none","noone","nor","normally","not","nothing","novel","now","nowhere","obviously","of","off",
		"often","oh","ok","okay","old","on","once","one","ones","only","onto","or","other","others","otherwise",
		"ought","our","ours","ourselves","out","outside","over","overall","own","particular","particularly","per",
		"perhaps","placed","please","plus","possible","presumably","probably","provides","que","quite","qv","rather",
		"rd","re","really","reasonably","regarding","regardless","regards","relatively","respectively","right","said",
		"same","saw","say","saying","says","second","secondly","see","seeing","seem","seemed","seeming","seems","seen",
		"self","selves","sensible","sent","serious","seriously","seven","several","shall","she","should","shouldn't",
		"since","six","so","some","somebody","somehow","someone","something","sometime","sometimes","somewhat",
		"somewhere","soon","sorry","specified","specify","specifying","still","sub","such","sup","sure","t's","take",
		"taken","tell","tends","th","than","thank","thanks","thanx","that","that's","thats","the","their","theirs",
		"them","themselves","then","thence","there","there's","thereafter","thereby","therefore","therein","theres",
		"thereupon","these","they","they'd","they'll","they're","they've","think","third","this","thorough",
		"thoroughly","those","though","three","through","throughout","thru","thus","to","together","too","took",
		"toward","towards","tried","tries","truly","try","trying","twice","two","un","under","unfortunately","unless",
		"unlikely","until","unto","up","upon","us","use","used","useful","uses","using","usually","value","various",
		"very","via","viz","vs","want","wants","was","wasn't","way","we","we'd","we'll","we're","we've","welcome",
		"well","went","were","weren't","what","what's","whatever","when","whence","whenever","where","where's",
		"whereafter","whereas","whereby","wherein","whereupon","wherever","whether","which","while","whither","who",
		"who's","whoever","whole","whom","whose","why","will","willing","wish","with","within","without","won't",
		"wonder","would","would","wouldn't","yes","yet","you","you'd","you'll","you're","you've","your","yours",
		"yourself","yourselves","zero"
	]
};
