<?

require_once 'SecretSanta.php';

class SecretSantaTest extends PHPUnit_Framework_TestCase
{

  protected $santa;

  protected function setUp() {

    $this->santa = new SecretSanta("test-names.txt");

  }

  public function testGetParticipantsFromFile() {

    $this->assertEquals(9, count($this->santa->participants));
    $this->assertTrue( $this->santa->participants[0]->first_name == "Tim" );
    $this->assertTrue( $this->santa->participants[8]->email == "<eric@example.com>" );

  }

  /**
   * @dataProvider alreadyChosenDataProvider
   */
  public function testAlreadyChosen($participant, $participant_pairs) {

    $this->assertTrue ( $this->santa->alreadyChosen($participant, $participant_pairs) );

  }

  public function alreadyChosenDataProvider () {

    $participant = new Participant("Test","Ifchosen","<test@example.com>");

    return array(
      array(
        $participant,
        array( 
          array(
            "giver" => new Participant("Random","Person","<random@example.com>"),
            "receiver" => new Participant("Another","Random","<random2@example.com>")
            ),
            array(
            "giver" => new Participant("Third","Person","<third@example.com>"),
            "receiver" => $participant
            )
          )
        )
      );
  }

  public function testGetParticipantPairs() {

    foreach ($this->santa->participant_pairs as $pair) {

      // nobody is giving to their own family member
      $this->assertTrue ( $pair["giver"]->family_name != $pair["receiver"]->family_name);
      
      // nobody is giving to themselves
      $this->assertTrue ( ! $pair["giver"]->matches($pair["receiver"]));

    }

  }

}

?>
