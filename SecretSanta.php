<?

// NOTE: set MAX_DRAWS in proportion to 
// the number of supported participants

define('MAX_DRAWS',100);

require 'Participant.php';

class SecretSanta {

	public $participants;
	public $participant_pairs;

	public function __construct($file) {

		$this->participants = $this->getParticipantsFromFile($file);

		// keep trying until we find a successful pairing
		$try_again = true;
		while($try_again) {
			try {
				$try_again = false;
				$this->participant_pairs = $this->getParticipantPairs($this->participants);
			} catch (Exception $e) {
				$try_again = true;
			}
		}

	}

	// takes an input file and returns an associative array of participants
	function getParticipantsFromFile($file) {

		$participants = array();	
		$fh = fopen($file, "r");

		while (true) {
			$line = fgets($fh);
			if (empty($line)) break; 
		 	$participant_data = explode(" ", str_replace("\n", "",$line));
		 	$participant = new Participant($participant_data[0], $participant_data[1], $participant_data[2]);
		 	array_push($participants, $participant);
		}

		return $participants;
	}

	function getRandomParticipant($participants) {
		$num_participants = count($participants);
		$random_index = rand(0, $num_participants - 1);
		return $participants[$random_index];
	}

	// is $participant already a "receiver" in $participant_pairs?
	function alreadyChosen ($participant, $participant_pairs) {
		$chosen = false;
		foreach($participant_pairs as $pair) {
			if ($pair["receiver"]->matches($participant)) {
				$chosen = true;
			} 
		}
		return $chosen;
	}

	// takes an array of participants and
	// returns an array of participant pairs
	function getParticipantPairs($participants) {
		$participant_pairs = array();

			foreach ($participants as $participant) {
				$random_participant = $this->getRandomParticipant($participants);
				$counter = 0;

				while( $participant->inSameFamilyAs($random_participant) || 
					     $this->alreadyChosen($random_participant, $participant_pairs) ) {
					$random_participant = $this->getRandomParticipant($participants);			
					$counter++;
					if ($counter > MAX_DRAWS) {
						// we've reached a dead-end / infinite loop, try again
						throw new Exception();
					}
				}

				array_push($participant_pairs, array(
						"giver" => $participant,
						"receiver" => $random_participant
					)  );
			}
		return $participant_pairs;
	}

	function displayPairs () {
		echo ("Giver => Receiver\n==================\n");
		foreach($this->participant_pairs as $pair) {
			echo ($pair["giver"]->displayName() . " => " . $pair["receiver"]->displayName() . "\n");
		}
	}

	function emailGiftGivers() {

		foreach($this->participant_pairs as $pair) {
			$to = $pair["giver"]->displayName() . " " . $pair["giver"]->email; 
			$message = "Dear " . $pair["giver"]->first_name . ",\n\n";
			$message .= "Your Secret Santa Recipient is: " . $pair["receiver"]->displayName() . "\n\n";
			$message .= "Make sure to give an awesome gift!\n\n";
			$message .= "Sincerely,\n\nThe Secret Santa Administrator\n\n";
			$headers = 'From: <secretsanta@example.com>';

			// TODO: send actual message [requires configured mail server]
			// mail($to, "Your SecretSanta Santa Recipient", $message, $headers); 

		}
	}

}

if (empty($argv[1])) {
	echo ("ERROR: Filename not specified\nProper Usage: php SecretSanta.php <filename>");
} else {
	$santa = new SecretSanta($argv[1]);
	$santa->displayPairs(); 
	$santa->emailGiftGivers();
}

?>

