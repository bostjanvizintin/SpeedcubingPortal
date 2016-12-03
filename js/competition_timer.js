		var counting = false;
		var time = 0;
		var seconds = 0, minutes = 0, hours = 0, t;
		var bestTime = Number.MAX_VALUE;;
		var worstTime = 0;
		var sumOfTimes = 0;
		var times5 = [];
		var times12 = [];
		var numOfSolves = 0;
		var scramble = scrambles.pop();
		
		window.addEventListener("keypress", keyPressed, false);

		function milisecondsToTime(milisec){

			var milisec = milisec;

			var hours = Math.floor((milisec/(1000*60*60))%24);
			var minutes = Math.floor((milisec/(1000*60))%60);
			var seconds = Math.floor((milisec/1000)%60);
			var miliseconds = Math.ceil((milisec%1000)/10);
			
			if(minutes == 0)
				return seconds + "." + miliseconds;
			else if(hours == 0)
				return minutes + ":" + seconds + "." + miliseconds;
			else
				return hours + ":" + minutes + ":" + seconds + "." + miliseconds; 
			

		}

		function keyPressed(e) {
				if (e.keyCode == "32" && !counting) {
		    		timer();
		    		console.log("starting");
		    		var d = new Date();
					time = d.getTime();
					document.getElementById("time").innerHTML = milisecondsToTime(0);
					document.getElementById("scramble").innerHTML = scrambles[scrambles.length -1];
		    	}
		    	if (e.keyCode == "32" && counting) {
		    		clearTimeout(t);
		    		console.log("stopping");
		    		var d = new Date();
		    		time = d.getTime() - time;
		    		seconds = 0;
		    		minutes = 0;
		    		hours = 0;
		    		console.log(time);
		    		console.log("time: " + milisecondsToTime(time));
		    		document.getElementById("time").innerHTML = milisecondsToTime(time); 
					document.getElementById("times").innerHTML = "<li>"+ milisecondsToTime(time) +"</li>" + document.getElementById("times").innerHTML;

		    		if(time > worstTime){
		    			worstTime = time;
		    			document.getElementById("worst").innerHTML = "<strong>Worst time: </strong>" + milisecondsToTime(worstTime);
		    		}

		    		if(time < bestTime){
		    			bestTime = time;
		    			document.getElementById("best").innerHTML = "<strong>Best time: </strong>" + milisecondsToTime(bestTime);
		    		}

		    		numOfSolves++;
		    		document.getElementById("numOfSolves").innerHTML = "<strong>Number of solves: </strong>" + numOfSolves;
		    		sumOfTimes += time;

		
		    		times5.push(time);
		    		if(times5.length > 4){
		    			times5.shift();
		    			document.getElementById("mean5").innerHTML = "<strong>Mean of 5: </strong>" + milisecondsToTime(times5.reduce(function(a, b) { return a + b; }, 0) /  5);
					
					}
		    		
		    		times12.push(time);
		    		if(times12.length > 11){
		    			times12.shift();
		    		document.getElementById("mean12").innerHTML = "<strong>Mean of 12: </strong>" + milisecondsToTime(times12.reduce(function(a, b) { return a + b; }, 0) /  12);
		    		}

		    		document.getElementById("mean").innerHTML = "<strong>Mean: </strong>" + milisecondsToTime(sumOfTimes/numOfSolves);


		    		//add time to database(ajax request to php/add_comp_time.php)
		    		var httpRequest;
					if (window.XMLHttpRequest) { // Mozilla, Safari, IE7+ ...
					    xmlhttp = new XMLHttpRequest();
					} else if (window.ActiveXObject) { // IE 6 and older
					    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
					}
		    		xmlhttp.open("GET","../src/php/add_comp_time.php?time=" + time +"&scramble=" + scramble  ,true);
					xmlhttp.send();

					scramble = scrambles.pop();
					if(scramble == null){
						window.removeEventListener("keypress", keyPressed, false);
						window.setTimeout(function(){window.location.href = "http://student.famnit.upr.si/~89111190/npb/src/php/unset_comp_entry.php";}, 2000);
						//window.alert('Your average is: ' + milisecondsToTime(sumOfTimes/numOfSolves) + '. Thank you for competing.');
					}	
					addScramble();
		    	}
		    counting = !counting;
		   }

		function add() {
		    seconds++;
		    if (seconds >= 60) {
		        seconds = 0;
		        minutes++;
		        if (minutes >= 60) {
		            minutes = 0;
		            hours++;
		        }
		    }
		document.getElementById("time").innerHTML = milisecondsToTime(hours*60*60*1000+minutes*60*1000+seconds*1000);    
	    console.log(seconds + ',' + minutes + ',' + hours);

	    timer();
		}

		function timer() {
		    t = setTimeout(add, 1000);
		}

		function addScramble(){
			document.getElementById("scramble").innerHTML = scramble;
			//console.log(scramble);

		}