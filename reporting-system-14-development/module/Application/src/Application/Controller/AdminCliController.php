<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Crypt\Password\Bcrypt as Bcrypt;
use ZfcUser\Controller\UserController;

use Application\Entity\MList;

class AdminCliController extends AbstractActionController
{
   

    public function testSave(){
    }
  
    public function importMemberAction(){
        $member_srv = $this->getServiceLocator()->get('MemberService');
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $repo = $em->getRepository('Application\Entity\User');


		
		$usr='';
		$usr=$this->params()->fromQuery('usr');

		if($usr == 'export'){
			$users = $repo->findAll();
			$fh = fopen('/var/www/amj_reports/data/cache/fout','w');
			fwrite($fh,"User count is :".sizeof($users)."\n");
			foreach ($users as $user){
				fwrite($fh,'"'.$user->getDisplayName().'",'.$user->getMembercode().',"'.$user->getEmail().'",'.$user->getPhonePrimary().','.$user->getPhoneAlternate()."\n");
			}
			var_dump(fstat($fh));
			fclose($fh);
			exit(1);
		}
		
		$file=$this->params()->fromQuery('src');

		if(count($file)>0){
			$location="/var/www/amj_reports/data/import";

			$CODE=0;$NAME=1;$BRCODE=2;$BRNAME=3;
			$row = 1;
			$errors="";
			$exists=0;
			if (($handle = fopen($location."/".$file, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 5000, ",","\"")) !== FALSE) {
					$num = count($data);
					if($num !=4) {
							$errors .= "<p>Invalid parseing $num for $row: $data[0]<br /></p>\n";
					}else{
									$list = $member_srv->getMemberByHash(($data[$CODE]) );
									if($list==null || (is_array($list) && sizeof($list)<1) ){

										$mlist = new MList();

										$mlist->setMemberCode($data[$CODE]);
										$mlist->setStatus('active');
										$mlist->setDisplayName($data[$NAME]);
										$mlist->setLocationCode($data[$BRCODE]);
										$mlist->setBranchName($data[$BRNAME]);

										$member_srv->updateMlist($mlist,$this->current_user() );
										$row++;
									}else{
										$exists++;
									}
					}
			}
			}else{
				$errors .= "<p>Invalid Import command $file <br /></p>\n";
			}
			fclose($handle);
		}

        return  new ViewModel(array('result'=>array('records'=>$row,'errors'=>$errors,'exists'=>$exists)) );
    }


	public function testAction(){
        $member_srv = $this->getServiceLocator()->get('MemberService');

		$list = $member_srv->getMemberByHash(sha3("abc123"));        
		print "size[".sizeof($list)."]";
		print "array?[".is_array($list)."]";
		print "if[".(!is_array($list) || sizeof($list)<1)."]";

		if(!is_array($list) || sizeof($list)<1){
			print "In if[".(!is_array($list) || sizeof($list)<1)."]";

		}
		$list = $member_srv->getMemberByHash(sha3("abc123") );        
		

        $result = " This will import memnber info and replace existing info [".($member_srv!=null)."]";

        $result .= " This will import memnber info and replace existing info [".($list!=null)."]";

        $result .='<pre>'. var_dump($list).'</pre>';

        print($result."\n");

		$config = $this->getServiceLocator()->get('Configuration');

	}    

	private $nationalGSID=2146;

	public function setupSampleDbAction(){

		//Update Reports
		//$reprotsSrv = $this->getServiceLocator()->get('ReportSubmissionService');
		//$qb = $reprotsSrv->createReportsDataSource($this->nationalGSID)->getData();

		//print(count($qb->getQuery()));
		#$dbCleanUpService = $this->getServiceLocator()->get('DBCleanupService');
		#$dbCleanUpService->cleanReports();
		//$this->updateNamesAndUsername();

	}


	public function updateSampleUsers(){

		//Will setup user attributes to Test subjects
		//we assume that OfficeAssignements is setup 
		//so that all users linked to active/valid office assignements are 
		//to be converted to test subjects
		// All detailed login is in DBCleanUpService
		$dbCleanUpService = $this->getServiceLocator()->get('DBCleanupService');

		//print(count($dbCleanUpService->getOffices($nationalGSID))."\n");

		//2019-11-28
	}

	public function updateNamesAndUsername(){
		$dbCleanUpService = $this->getServiceLocator()->get('DBCleanupService');
		$dbCleanUpService->cleanUpNames($this->names,'lajna.ca');
	}

	public function encryptSample(){

		print("Number\n".$dbCleanUpService->encryptSample('1234')."\n");

		print("Date\n".$dbCleanUpService->encryptSample('2019-12-05')."\n");

		print("Text\n".$dbCleanUpService->encryptSample('Text Value')."\n");

		print('Memo'."\n".$dbCleanUpService->encryptSample('<p><br></p>
			<p>Froala Editor is a lightweight WYSIWYG <strong>HTML&nbsp;</strong>Editor written in Javascript that enables rich text editing capabilities for your applications.&nbsp;</p>
			<p>Its complete <a href="/wysiwyg-editor/v2.0/docs" title="Documentation">&nbsp;documentation</a>, specially designed <a href="#frameworks" title="Frameworks">&nbsp;framework plugins</a> and <a href="/wysiwyg-editor/v2.0/examples" title="Examples">tons of examples</a> make it easy to integrate. We&#39;re continuously working to add in new features and take the <em>Javascript&nbsp;</em>web <u><em>WYSIWYG&nbsp;</em></u>editing capabilitie<strong><em>s beyond its current l</em></strong>imits.</p>
			<ol>
				<li>Sample&nbsp;</li>
				<li>Second</li>
				<li>Third</li>
			</ol>
			')."\n");

	}

	private $names = array('Aabdeen',
'Aabid',
'Aadam',
'Aadil',
'Aafiya',
'Aahil',
'Aaish',
'Aakif',
'Aalam',
'Aalee',
'Aalim',
'Aamil',
'Aamir',
'Aaqib',
'Aaqil',
'Aarif',
'Aariz',
'Aashiq',
'Aashir',
'Aasif',
'Aasim',
'Aatif',
'Aatiq',
'Aayid',
'Aazad',
'Aazim',
'Abaan',
'Abadiyah',
'Abahh',
'Aban',
'Aban',
'Abbaad',
'Abbaas',
'Abbad',
'Abbas',
'Abd-Al',
'Abdah',
'Abdnan',
'Abdul',
'Abdul-Adl',
'Abdul-Afuw',
'Abdul-Ahad',
'Abdul-Ali',
'Abdul-Alim',
'Abdul-Awwal',
'Abdul-Azim',
'Abdul-Baasid',
'Abdul-Badee',
'Abdul-Baith',
'Abdul-Baseer',
'Abdul-Batin',
'Abdul-Fattaah',
'Abdul-Fattah',
'Abdul-Ghani',
'Abdul-Haadi',
'Abdul-Hafeez',
'Abdul-Hafiz',
'Abdul-Hai',
'Abdul-Halim',
'Abdul-Hamid',
'Abdul-Hannan',
'Abdul-Hayy',
'Abdul-Jawwad',
'Abdul-Kabir',
'Abdul-Kader',
'Abdul-Khabir',
'Abdul-Maalik',
'Abdul-Majeed',
'Abdul-Mani',
'Abdul-Mannan',
'Abdul-Mateen',
'Abdul-Mubdee',
'Abdul-Mueed',
'Abdul-Muhaymin',
'Abdul-Muhsin',
'Abdul-Muhyee',
'Abdul-Munim',
'Abdul-Muntaqim',
'Abdul-Muqeet',
'Abdul-Muqsit',
'Abdul-Musawwir',
'Abdul-Muti',
'Abdul-Muzanni',
'Abdul-Nafi',
'Abdul-Naseer',
'Abdul-Qadeer',
'Abdul-Qadir',
'Abdul-Qayoom',
'Abdul-Qayyum',
'Abdul-Quddoos',
'Abdul-Rabb',
'Abdul-Rafi',
'Abdul-Raqib',
'Abdul-Tawwab',
'Abdul-Waahid',
'Abdul-Waali',
'Abdul-Wajid',
'Abdul-Wakil',
'Abdul-Waliy',
'Abdul-Wasi',
'Abdullah',
'Abdulrahman',
'Abdur-Raheem',
'Abdur-Rahmaan',
'Abdur-Raqeeb',
'Abdur-Rasheed',
'Abdur-Razzaaq',
'Abdus-Sabur',
'Abdus-Salaam',
'Abdus-Salam',
'Abdus-Samad',
'Abdus-Sami',
'Abdus-Sattar',
'Abdus-Shafi',
'Abdus-Smad',
'Abdus-Subbooh',
'Abdush-Shahid',
'Abdus',
'Abdut-Tawwab',
'Abed',
'Abedin',
'Abid',
'Abood',
'Abrad',
'Abrar',
'Abrash',
'Absi',
'Abu-Bakr',
'Abul-Khayr',
'Abyad',
'Abzari',
'Adam',
'Adawi',
'Adbul',
'Adeeb',
'Adeel',
'Adel',
'Adham',
'Adi',
'Adib',
'Adil',
'Adiy',
'Adl',
'Adnaan',
'Adnan',
'Aduz-Zahir',
'Adyan',
'Aejaz',
'Afdaal',
'Afdal',
'Afeef',
'Affan',
'Afif',
'Aflah',
'Aftab',
'Afzal',
'Agha',
'Agharr',
'Ahad',
'Ahmad',
'Ahmar',
'Ahmed',
'Ahnaf',
'Ahsan',
'Ahwas',
'Ahzab',
'Aidh',
'Aiman',
'Ajlah',
'Ajmal',
'Ajwad',
'Akbar',
'Akbar',
'Akeem',
'Akhas',
'Akhfash',
'Akhlaq',
'Akhtar',
'Akif',
'Aklamash',
'Akmal',
'Akram',
'Al-Abbas',
'Alaa',
'Alaa-Udeen',
'Alaaudeen',
'Alabbas',
'Alaleem',
'Alam',
'Alamgir',
'Alawi',
'Ali',
'Alif',
'Alih',
'Alim',
'Allam',
'Almas',
'Altaf',
'Alvi',
'Amaan',
'Amaanullah',
'Amal',
'Amam',
'Ameen',
'Ameer',
'Amin',
'Amir',
'AMIR',
'Amjad',
'Ammaar',
'Ammar',
'Amr',
'Amru',
'Anahid',
'Anas',
'Anasah',
'Anass',
'Anees',
'Aniq',
'Anis',
'Anjum',
'Anjuman',
'Anna',
'Annnees',
'Antar',
'Antarah',
'Anwaar',
'Anwar',
'Anwer',
'Aqeel',
'Aqib',
'Aqil',
'Arab',
'Arafaat',
'Arafat',
'Arbad',
'Areeb',
'Arhab',
'Arif',
'Arkaan',
'Armaan',
'Arqam',
'Arsalaan',
'Arsh',
'Arshad',
'Arshad',
'Arshaq',
'Artah',
'Arwarh',
'Asad',
'Asbagh',
'Asbat',
'Aseed',
'Aseel',
'Asfa',
'Asgar',
'Asghar',
'Ashfaq',
'Ashqar',
'Ashraf',
'Asif',
'Asil',
'Asir',
'Askari',
'Aslam',
'Asmar',
'Ataa',
'Atif',
'Aula',
'Awad',
'Awn',
'Awni',
'Aws',
'Awwab',
'Awwal',
'Ayaan',
'Ayaaz',
'Ayan',
'Ayaz',
'Aybak',
'Ayham',
'Ayman',
'Ayoob',
'Ayub',
'Ayyash',
'Ayyoob',
'Azam',
'Azaz',
'Azb',
'Azeem',
'Azhaar',
'Azhar',
'Azim',
'Aziz',
'Azmat',
'Azmi',
'Azraq',
'Azraqi',
'Azzaam',
'Azzam',
'Baahir',
'Baaqir',
'Baari',
'Baariq',
'Baasha',
'Baasim',
'Baasit',
'Badi',
'Badiy',
'Badr',
'Badr-Udeen',
'Badraan',
'Badri',
'Badruddeen',
'Baha',
'Baha-Udeen',
'Bahij',
'Bahiy-Udeen',
'Baid',
'Bais',
'Bakar',
'Bakir',
'Bakr',
'Balagh',
'Bandar',
'Baqir',
'Bari',
'Barr',
'Basaam',
'Basair',
'Basel',
'Bashaar',
'Basheer',
'Basil',
'Basim',
'Basir',
'Bassam',
'Bassil',
'Batin',
'Bayan',
'Baz',
'Bihzad',
'Bilaal',
'Bilal',
'Bishr',
'Bishr',
'Boutros',
'Burhaan',
'Daafi',
'Daai',
'Daamin',
'Daamir',
'Daanish',
'Daanyal',
'Daawood',
'Dabbah',
'Daghfal',
'Dahhak',
'Daif',
'Daifallah',
'Dakhil',
'Daleel',
'Dalil',
'Damdam',
'Dameer',
'Damian',
'Damurah',
'Dara',
'Darim',
'Dawar',
'Dawlah',
'Dawood',
'Dawoud',
'Dawud',
'Daylam',
'Dayyan',
'Deen',
'Deenar',
'Dhaafir',
'Dhaahir',
'Dhaakir',
'Dhaki',
'Dhareef',
'Dhiya',
'Dhul-Fiqaar',
'Dihyah',
'Dihyat',
'Dilawar',
'Dilbar',
'Dildar',
'Dilshad',
'Dinar',
'Diwan',
'DIYA',
'Diyaa-Udeen',
'Duha',
'Durrah',
'Eijaz',
'Emad',
'Enam',
'Faadi',
'Faadil',
'Faaid',
'Faaiq',
'Faakhir',
'Faalih',
'Faaris',
'Faarooq',
'Faatih',
'Faatin',
'Faatir',
'Fadi',
'Fadil',
'Fadl',
'Fadl-Ullah',
'Faeq',
'Fahad',
'Fahd',
'Fahd',
'Faheem',
'Fahim',
'Fahmi',
'Faiq',
'Faisal',
'Faiyaz',
'Faiz',
'Faizan',
'Faizel',
'Fakeeh',
'Fakhir',
'Fakhr',
'Fakhri',
'Fakhri',
'Fakhry',
'Fakih',
'Falah',
'Faliq',
'Faqeeh',
'Faqih',
'Faqih',
'Faqir',
'Farafisa',
'Farag',
'Faraj',
'Farajallah',
'Faraqlit',
'Farasat',
'Faraz',
'Fard',
'Fareed',
'Farhan',
'Farhat',
'Farid',
'Farid',
'Fariq',
'Faris',
'Farookh',
'Farooq',
'Farqad',
'Farrukh',
'Faruq',
'Farwah',
'Faseeh',
'Fastiq',
'Fateen',
'Fateh',
'Fathi',
'Fatih',
'Fatik',
'Fattah',
'Fawad',
'Fawaz',
'Fawwaz',
'Fawzan',
'Fawzi',
'Fawzi',
'Fayd',
'Fayiz',
'Faysal',
'Fayyaad',
'Fayyadh',
'Fayzan',
'Fazil',
'Feroze',
'Fida',
'Fidaa',
'Fiddah',
'Fihr',
'Fikri',
'Firas',
'Firdaus',
'Firdaus',
'Firdos',
'Firoz',
'Fouad',
'Fuad',
'Fudail',
'Fudayl',
'Fujai',
'Furqaan',
'Furqau',
'Fuwad',
'Gabir',
'Gabr',
'Gadi',
'Gafar',
'Galal',
'Gamal',
'Gamil',
'Ghaali',
'Ghaalib',
'Ghaamid',
'Ghaazi',
'Ghafir',
'Ghaith',
'Ghalib',
'Ghanem',
'Ghani',
'Ghannam',
'Ghasaan',
'Ghashiah',
'Ghassan',
'Ghauth',
'Ghawth',
'Ghaylan',
'Ghayoor',
'Ghayth',
'Ghayur',
'Ghazalan',
'Ghazawan',
'Ghazi',
'Ghazzal',
'Ghiyaath',
'Ghiyas',
'Ghiyath',
'Ghufran',
'Ghulam',
'Ghunayn',
'Ghutayf',
'Gohar',
'Guda',
'Gul',
'Gulab',
'Gulfam',
'Gulshan',
'Gulzar',
'Haady',
'Haafil',
'Haajid',
'Haamid',
'Haani',
'Haarith',
'Haaroon',
'Haashid',
'Haashim',
'Haatim',
'Haazim',
'Habash',
'Habeeb',
'Habeel',
'Habib',
'Habibullah',
'Habis',
'Haddad',
'Hadee',
'Hadi',
'Hadid',
'Hadis',
'Hadrami',
'Hafeez',
'Hafi',
'Hafiz',
'Hafs',
'Haider',
'Haitham',
'Hajib',
'Hajjaj',
'Hakam',
'Hakeem',
'Hakim',
'Haleef',
'Halim',
'Hallaj',
'Halwani',
'Hamad',
'Hamas',
'Hamd',
'Hamdaan',
'Hamdan',
'Hamdi',
'Hameem',
'Hami',
'Hamid',
'Hamim',
'Hammad',
'Hammam',
'Hamood',
'Hamshad',
'Hamza',
'Hamzah',
'Hananan',
'Hanash',
'Haneef',
'Hani',
'Hanif',
'Hanifah',
'Hanlala',
'Hannan',
'Hanzalah',
'Haqq',
'Haraam',
'Haris',
'Harith',
'Hariz',
'Haroon',
'Harun',
'Hasan',
'Haseen',
'Hashid',
'Hashim',
'Hashir',
'Hashmat',
'Hasib',
'Hasim',
'Hassan',
'Hassib',
'Hatib',
'Hatim',
'Hawshab',
'Hayaat',
'Hayder',
'Haytham',
'Hayyan',
'Hazim',
'Hazir',
'Hazm',
'Hazrat',
'Hibah',
'Hibbaan',
'Hidayat',
'Hikmat',
'Hilaal',
'Hilal',
'Hilmi',
'Himayat',
'Hirz',
'Hishaam',
'Hosni',
'Houd',
'Hubaab',
'Hud',
'Hudhaifa',
'Hujayyah',
'Hujjat',
'Humaid',
'Humaidaan',
'Humam',
'Humayl',
'Humayun',
'Hunaid',
'Hunayn',
'Huraira',
'Hurayth',
'Hurmat',
'Hurrah',
'Husaam',
'Husam',
'Husni',
'Hussain',
'Hussein',
'Huthayfa',
'Huzayfah',
'Huzayl',
'Hyder',
'Ibrahim',
'Idrees',
'Idris',
'Iesa',
'Ifran',
'Iftikhar',
'Ihaab',
'Ihab',
'Ihab',
'Ihsaan',
'Ihsan',
'Ihtesham',
'Ihtiram',
'Ijaz',
'Ijli',
'Ikhlas',
'Ikram',
'Ikrimah',
'Ilaahi',
'Ilash',
'Ilifat',
'Ilyaas',
'Ilyas',
'Imaad',
'Imaad-Udeen',
'Imad',
'Imam',
'Imraan',
'Imran',
'Imtiyaz',
'Inaam',
'Inas',
'Inayat',
'Inshirah',
'Intakhab',
'Intikhab',
'Intizar',
'Iqbal,-iqbaal',
'Iqmal',
'Iqrit',
'Iqtidar',
'Irfaan',
'Irfan',
'Irshad',
'Isa',
'Isaam',
'Isabhani',
'Isam,-isaam',
'Ishaaq',
'Ishaq',
'Ishrat',
'Ishtiyaq',
'Iskafi',
'Iskandar',
'Islam',
'Ismaael',
'Ismad',
'Ismaeel',
'Ismah',
'Israail',
'Istakhri',
'Itban',
'Ithaar',
'Itimad',
'Iyaad',
'Iyaas',
'Izhar',
'Izz',
'Izz-Udeen',
'Izzaldin',
'Izzat',
'Jaabir',
'Jaad',
'Jaadallah',
'Jaafar',
'Jaan',
'Jaarallah',
'Jaasim',
'Jaasir',
'Jabal',
'Jabalah',
'Jabbar',
'Jabez',
'Jabir',
'Jabir',
'Jabr',
'Jabril',
'Jad',
'Jafar',
'Jahanafirin',
'Jahangir',
'Jahdami',
'Jahdari',
'Jahiz',
'Jahm',
'Jahsh',
'Jalaal',
'Jalal',
'Jalees',
'Jalil',
'Jalil',
'Jam,Aan',
'Jamaal',
'Jamaal-Udeen',
'Jamal',
'Jameel',
'Jamil',
'Jammaz',
'Jamshed',
'Jaraah',
'Jareer',
'Jariyah',
'Jarood',
'Jasim',
'Jasiyah',
'Jasoor',
'Javed',
'Jawad',
'Jawdan',
'Jawdat',
'Jawhar',
'Jibran',
'Jibril',
'Jihaad',
'Jihad',
'Jinan',
'Jiyad',
'Jnhih',
'Juayl',
'Jubair',
'Juda',
'Juday',
'Jugnu',
'Juhaym',
'Jumail',
'Jumanah',
'Jummal',
'Junaid',
'Junayd',
'Jundub',
'Juthamah',
'Kaab',
'Kaalim',
'Kaamil',
'Kaarim',
'Kaashif',
'Kabir',
'Kadeem',
'Kafeel',
'Kafi',
'Kahill',
'Kajji',
'Kalam',
'Kalbi',
'Kaleem',
'Kalim',
'Kamaal',
'Kamaaluddeen',
'Kamal',
'Kameel',
'Kamil',
'Kamish',
'Kanaan',
'Karam',
'Karamah',
'Kareem',
'Karim',
'Kashef',
'Kasib',
'Kasir',
'Kateb',
'Katheer',
'Kathir',
'Kausar',
'Kawkab',
'Kawthar',
'Kaysan',
'Kazim',
'Khaalid',
'Khabir',
'Khadim',
'Khair-Udeen',
'Khairi',
'Khairy',
'Khalaf',
'Khaldoon',
'Khaldun',
'Khaleed',
'Khaleefa',
'Khaleel',
'Khaleeq',
'Khalid',
'Khalifah',
'Khalil',
'Khaliq',
'Khalis',
'Khallad',
'Khallaq',
'Khannas',
'Kharijah',
'Khasib',
'Khateeb',
'Khawwat',
'Khayr',
'Khayrat',
'Khayri',
'Khayyam',
'Khayyat',
'Khazin',
'Khidash',
'Khidr',
'Khinzeer',
'Khirash',
'Khorshed',
'Khubayb',
'Khulayd',
'Khunays',
'Khuraym',
'Khuraymah',
'Khursheed',
'Khurshid',
'Khush-Bakht',
'Khuzaymah',
'Khwaja',
'Kifayat',
'Kishwar',
'Kulthum',
'Kurayb',
'Labeeb',
'Labeed',
'Labib',
'Laiq',
'Laith',
'Lajlaj',
'Laqeet',
'Latif',
'Layth',
'Layzal',
'Limazah',
'Liyaqah',
'Liyaqat',
'Luqmaan',
'Luqman',
'Lut',
'Lutf',
'Lutfi',
'Luwai',
'Maroof',
'Maahi',
'Maahir',
'Maaiz',
'Maajid',
'Maarij',
'Maaz',
'Maazin',
'Mabad',
'Madani',
'Mahbub',
'Mahdi',
'Mahdy',
'Maheen',
'Maher',
'Mahfooz',
'Mahfuz',
'Mahib',
'Mahid',
'Mahja',
'Mahmood',
'Mahomet',
'Mahtab',
'Mahud',
'Mahuroos',
'Maisara',
'Maisoon',
'Majd',
'Majd-Udeen',
'Majdi',
'Majdy',
'Majid',
'Makeen',
'Makhdoom',
'Makki',
'Makram',
'Malak',
'Malih',
'Malik',
'Mamduh',
'Mamnoon',
'Mannat',
'Mansour',
'Mansur',
'Manzar',
'Manzoor',
'Maqbul',
'Maqil',
'Maqsud',
'Marghoob',
'Maruf',
'Marufirah',
'Marwaan',
'Marwan',
'Marzuq',
'Masarrat',
'Mashal',
'Masheer',
'Mashhud',
'Mashkoor',
'Mashkur',
'Masoud',
'Masrur',
'Mastoor',
'Masud',
'Masum',
'Mateen',
'Matin',
'Matloob',
'Mawdud',
'Mawsil',
'Maymun',
'Maysarah',
'Mazeed',
'Mazhar',
'Mazin',
'Mehboob',
'Mehtab',
'Miftah',
'Mihammad',
'Mimar',
'Minhaj',
'Miqdaad',
'Miqdaam',
'Miraj',
'Mirsab',
'Mirza',
'Misal',
'Misbaah',
'Misbah',
'Misfar',
'Mishaari',
'Miskeen',
'Mistah',
'Mohammad',
'Momin',
'Moosa',
'Moosha',
'Muaaid',
'Muaath',
'Muammar',
'Muayid',
'Muaz',
'Muballigh',
'Mubarak',
'Mubashir',
'Mubashshir',
'Mubassir',
'Mubayyin',
'Mubin',
'Mudabbir',
'Muddathir',
'Mudhakkir',
'Mudrik',
'Mueen',
'Mueez',
'Mufaddal',
'Mufakkir',
'Mufallah',
'Mufarrij',
'Mufeed',
'Mufid',
'Muflih',
'Mufti',
'Mughith',
'Muhaajir',
'Muhafiz',
'Muhaimin',
'Muhajir',
'Muhammad',
'Muhannad',
'Muharrim',
'Muhdee',
'Muheet',
'Muhib',
'Muhriz',
'Muhsin',
'Muhtadi',
'Muhtady',
'Muhtashim',
'Muhyddeen',
'Mujaahid',
'Mujaddid',
'Mujahid',
'Mujazziz',
'Mujtaba',
'Mukarram',
'Mukhlis',
'Mukhtaar',
'Mukhtar',
'Mulayl',
'Mumin',
'Mumtaz',
'Munadi',
'Munaf',
'Munawwar',
'Mundhir',
'Muneeb',
'Muneef',
'Muneer',
'MUNIR',
'Munis',
'Munjid',
'Munkadir',
'Munqad',
'Munsif',
'Muntasir',
'Munthir',
'Munzir',
'Muqaddas',
'Muqaffa',
'Muqatadir',
'Muqbil',
'Muqtasid',
'Murabbi',
'Murad',
'Murarah',
'Mursal',
'Murshid',
'Murtaad',
'Murtadaa',
'Murtadhy',
'Musa',
'Musaaid',
'Musad',
'Musaddiq',
'Musawwir',
'Musharraf',
'Musheer',
'Mushfiq',
'Mushtaaq',
'Mushtaq',
'Muslih',
'Muslim',
'Mustaba',
'Mustaeen',
'Mustafa',
'Mustafa',
'Mustafeed',
'Mustahsan',
'Mustajab',
'Mustaneer',
'Mustaqeem',
'Mustatab',
'Muta-Ali',
'Mutaa',
'Mutahhir',
'Mutakabbir',
'Mutammam',
'Mutasim',
'Mutawassit',
'Mutaygab',
'Mutayyib',
'Mutazz',
'Mutee',
'Muthanna',
'Mutlaq',
'Muttaqi',
'Muttee',
'Muwafaq',
'Muwaffaq',
'Muzaffar',
'Muzakkir',
'Muzammil',
'Muzhir',
'Muzzammil',
'Naadir',
'Naaif',
'Naaji',
'Naajy',
'Naasif',
'Naasih',
'Naasiruddeen',
'Naathim',
'Naazil',
'Naazim',
'Nabeeh',
'Nabeel',
'Nabhan',
'Nabi',
'Nabigh',
'Nabih',
'Nabil',
'Nadeem',
'Nadheer',
'Nadhir',
'Nadim',
'Nadir',
'Nadira',
'Nadr',
'Naeem',
'Nafasat',
'Nafis',
'Nagib',
'Nahi',
'Naib',
'Nail',
'Naim',
'Najeeb',
'Najeed',
'Najeem',
'Naji',
'Najib',
'Najid',
'Najih',
'Najm',
'Najm-Udeen',
'Nakir',
'Naqeeb',
'Naqi',
'Naqid',
'Naqit',
'Naseem',
'Naseer',
'Nashat',
'Nashit',
'Nasif',
'Nasih',
'Nasim',
'Nasir',
'Nasirah',
'Nasr',
'Nassaar',
'NasserUdeen',
'Natheer',
'Natiq',
'Naushad',
'Naveed',
'Naveid',
'Nawaar',
'Nawaf',
'Nawf',
'Nawfal',
'Nayab',
'Nazakat',
'Nazeef',
'Nazeeh',
'Nazeer',
'Nazih',
'Nazim',
'Nazir',
'Nazmi',
'Neeshaan',
'Nehad',
'Nidal',
'Nihal',
'Nithar',
'Niyaz',
'Nizaam',
'Nizaar',
'Nizam',
'Nizar',
'Nizzar',
'Nooh',
'Noor',
'Noor-Udeen',
'Nuaym',
'Numaan',
'Numair',
'Numan',
'Nur',
'Nurani',
'Nuri',
'Nusayb',
'Nusayr',
'Parvaiz',
'Parwez',
'Parwiz',
'Pasha',
'Qaaid',
'Qaasim',
'Qabeel',
'Qabid',
'Qabil',
'Qabiz',
'Qader',
'Khan',
'Qadi',
'Qadim',
'Qadir',
'Qahhar',
'Qahir',
'Qaim',
'Qais',
'Qamar',
'Qani',
'Qanit',
'Qareeb',
'Qarib',
'Qaseem',
'Qasid',
'Qasim',
'Qatadah',
'Qays',
'Qayyam',
'Qayyim',
'Quasim',
'Qudamah',
'Qudrat',
'Quds',
'Quraish',
'Qurban',
'Qusay',
'Qusta',
'Qutaybah',
'Qutb',
'Qutbah',
'Qutub',
'Raadi',
'Raafi',
'Raaid',
'Raaji',
'Raakaan',
'Raakin',
'Raamiz',
'Raashid',
'Raatib',
'Rabah',
'Rabbani',
'Rabee',
'Rabi',
'Rabiah',
'Rabit',
'Radi',
'Raees',
'Rafee',
'Rafeeq',
'Rafi',
'Rafiq',
'Ragheb',
'Raghib',
'Rahat',
'Raheel',
'Raheem',
'Rahil',
'Rahim',
'Rahman',
'Rahmat',
'Raid',
'Raihaan',
'Rais',
'Rajaa',
'Rajab',
'Rakhshan',
'Ramadan',
'Ramalaan',
'Ramiz',
'Ramzi',
'Rana',
'Raonar',
'Raqib',
'Raseem',
'Rashaad',
'Rashad',
'Rashdan',
'Rasheed',
'Rasheeq',
'Rashid',
'Rasool',
'Ratiq',
'Rauf',
'Raunak',
'Ravoof',
'Rawahah',
'Rawdah',
'Rawh',
'Rayhan',
'Rayyan',
'Raza',
'Razak',
'Razeen',
'Raziq',
'Razzaq',
'Rehan',
'Rehman',
'Rehmat',
'RIAZ',
'Rida',
'Ridha',
'Ridhwan',
'Rifaah',
'Rifat',
'Rifat',
'Riyaal',
'Riyad',
'Riyadh',
'Riyasat',
'Riyaz',
'Riyyan',
'Rizwan',
'Rizwan',
'Roshan',
'Ruhani',
'Rukanah',
'Rushan',
'Rushd',
'Rushdi',
'Rushil',
'Rustam',
'Ruwaid',
'Ruwayfi',
'Saabiq',
'Saabir',
'Saad',
'Saadat',
'Saadiq',
'Saahir',
'Saajid',
'Saalih',
'Saalim',
'Saami',
'Saamir',
'Saarim',
'Saariyah',
'Sabaah',
'Sabah',
'Sabahat',
'Sabeeh',
'Sabiq',
'Sabir',
'Sabri',
'Sabur',
'Saburah',
'Sadaqat',
'Sadeed',
'Sadeem',
'Sadeeq',
'Sadi',
'Sadik',
'Sadiq',
'Sadiq',
'Sadoon',
'Sadr',
'Saeed',
'Safar',
'Safeenah',
'Safeer',
'Saffar',
'Safiy',
'Safiy',
'Safiyy',
'Saful-Islam',
'Safwan',
'Safwat',
'Sagheer',
'Sahab',
'Sahar',
'Sahel',
'Sahib',
'Sahil',
'Sahir',
'Sahl',
'Sahm',
'Saib',
'Said',
'Saif',
'Saji',
'Sajid',
'Sajjad',
'Sakeen',
'Sakhawat',
'Sakhr',
'Sakhrah',
'Salaah',
'Salah',
'Salah-Udeen',
'Salam',
'Salamah',
'Salamat',
'Salarjung',
'Saleel',
'Saleem',
'Saleet',
'Saleh',
'Salem',
'Salif',
'Salih',
'Salih',
'Salik',
'Salim',
'Salmaan',
'Salman',
'Salt',
'Samad',
'Sameer',
'Sameeullah',
'Sameh',
'Sami',
'Samiq',
'Samir',
'Samit',
'Sammak',
'Sanad',
'Sanawbar',
'Saood',
'Saqr',
'Saquib',
'Sariyah',
'Sarmad',
'Sarwar',
'Satih',
'Saud',
'Sawa',
'Sawad',
'Sawlat',
'Sawwaf',
'Sayaam',
'Sayeed',
'Sayf',
'Sayfudeen',
'Sayfiyy',
'Sayhan',
'Sayid',
'Sayyar',
'Sayyid',
'Seif',
'Shaady',
'Shaafi',
'Shaaheen',
'Shaahir',
'Shaakir',
'Shaamikh',
'Shaamil',
'Taaha',
'Taahir',
'Taahir',
'Taaj',
'Taajuddeen',
'Taalib',
'Taamir',
'Taariq',
'Taban',
'Tabassum',
'Tabish',
'Tabrez',
'Taha',
'Tahawwur',
'Tahir',
'Tahmeed',
'Tahoor',
'Tahseen',
'Taiseer',
'Taj',
'Tajammul',
'Tajim',
'Talal',
'Talat',
'Talha',
'Talhah',
'Tali',
'Talib',
'Tamam',
'Tamanna',
'Tameem',
'Tameez',
'Tamid',
'Tamkeen',
'Tammam',
'Tanwir',
'Taqdees',
'Taqi',
'Taqiyy',
'Tarannum',
'Tareef',
'Tarek',
'Tarfah',
'Tariq',
'Tarique',
'Tasadduq',
'Tasawwar',
'Taseen',
'Taslim',
'Tasneen',
'Tawbah',
'Tawfeeq',
'Tawfiq',
'Tawheed',
'Tawoos',
'Tawqir',
'Tawseef',
'Taymullah',
'Tayseer',
'Taysir',
'Tayyeb',
'Tayyib',
'Tazim',
'Thaabit',
'Thaamir',
'Thaaqib',
'Thabit',
'Thaman',
'Thamar',
'Thaqaf',
'Thaqib',
'Tharwat',
'Thauban',
'Thayer',
'Thumamah',
'Tihami',
'Tooba',
'Tufail',
'Tufayl',
'Tuhin',
'Tuhinsurra',
'Tulayb',
'Ubaadah',
'Ubadah',
'Ubaida',
'Ubaidah',
'Ubay',
'Ubayd',
'Ubaydullah',
'Ubayyi',
'Uhban',
'Ukkashah',
'Ulfat',
'Umaarah',
'Umair',
'Umar',
'Umarah',
'Umayr',
'Umayyah',
'Umer',
'Umran',
'Unais',
'Unays',
'Uqbah',
'Urooj',
'Urwah',
'Usaama',
'Usaamah',
'Usaid',
'Usama',
'Usamah',
'Usayd',
'Usman',
'Utbah',
'Uthmaan',
'Uthman',
'Uwais',
'Uwayam',
'Uways',
'Uzayr',
'Waahid',
'Waail',
'Waatiq',
'Wabisah',
'Waddaah',
'Wadi',
'Wadud',
'Wafa',
'Wafeeq',
'Wafi',
'Wafiq',
'Wahab',
'Wahb',
'Wahban',
'Waheed',
'Wahhab',
'Wahhaj',
'Wahi',
'Wahib',
'Wahid',
'Wail',
'Waiz',
'Wajahat',
'Wajdi',
'Wajeeb',
'Wajeeh',
'Wajid',
'Wajih',
'Wakalat',
'Wakee',
'Wakeel',
'Waleed',
'Wali',
'Walid',
'Waliyudeen',
'Waliyullah',
'Walleed',
'Wasaf',
'Waseef',
'Waseem',
'Wasi',
'Wasidali',
'Wassim',
'Wisaam',
'Xander',
'Xavier',
'Yaaemeen',
'Yaaseen',
'Yaasir',
'Yafi',
'Yaghnam',
'Yahya',
'Yalmaz',
'Yaman',
'Yameen',
'Yaqeen',
'Yaqoob',
'Yaqoot',
'Yaqub',
'Yar',
'Yasaar',
'Yaser',
'Yasin',
'Yasir',
'Yasoob',
'Yasser',
'Yathrib',
'Yawar',
'Yawqeer',
'Yazeed',
'Yoonus',
'Yoosuf',
'Yuhannis',
'Yuhans',
'Yunus',
'Yusr',
'Yusri',
'Yusuf',
'Zaafir',
'Zaahid',
'Zaahir',
'Zaaid',
'Zaamil',
'Zafar',
'Zaghlool',
'Zahid',
'Zaid',
'Zaid',
'Zaidaan',
'Zaigham',
'Zain',
'Zainuddeen',
'Zakariya',
'Zakariyya',
'Zaki',
'Zakir',
'Zakiy',
'Zalman',
'Zameel',
'Zamin',
'Zashil',
'Zayd',
'Zayyaan',
'Zeeshan',
'Zia',
'Ziad',
'Ziaul',
'Zikr',
'Ziyaad',
'Ziyad',
'Zohaib',
'Zohoor',
'Zoraiz',
'Zubair',
'Zubair',
'Zufar',
'Zuhair',
'Zuhayr',
'Zuhoor',
'Zukr',
'Zulaym',
'Zulfaqar',
'Zulkifl',
'Zunnoon',
'Zuraara');
}
/*
Number1234
==========
b0578c74c4f2c7ae7f0986739a7b3b2b6939c1b21a791c6c93f7be704c994703SpxMEiWI61o+LkPLWXWmUnsZ/60p850Vof1tZlkzSALw1J92odXSaFJJ83sSDZIlEJPvoV4H2VeC0HKwD4QlUg==

Date2019-12-05
==============
ab9ad9466f5c1c0154e68f1d1fe8ac78a617b5625407a3d220549fd809a3e3334Smcxkg0wE9fmw++Qpd5je/Ow2WMa4jgs7qBLdH8sP+/GRxkrgd6K6qdKPYp/7bRlvzR5Kjb0o9en+6B4aye8w==

TextValue
=========
fe92da743f033e86b3f1ae9459c0ed7d1e2582e780043c4760c45a295b5249dcPruuME5Asrxm2Bi1x7ctu9L1ftLfgikyyfAUafzqKepAcbSLA9QP4nNrxrGIv19pcD15Ys64smc+Z9MCLYNK5Q==

Memo
====
6b5eeca9a23b457de19ddf30cebeaf0c1e52731d134c2dd429881dc53cd6a12d1BNMKF4TJe1Y04dpL7jB40+c6aJT+T8Ssf7+ZKfh0a0kFkrYReNZ0CoS/ef6j4oWOAhTaGJA7KVwwtoX00qeAbCtynOm4cjOq7RcOHbIV5LGQugXtdNn7SuUKazXxAYoUbhoMjCsCujkr9pewbDdatXjBrC8Suta4lJnqMDrpJnfnPAuqLP/RdPsqmS24/mUJnd7vMW1JUJHD4Wb2sm5zJnvJLMkgjCfhsfsACGuZVkDoJYc4hgKLIan8bHNJ02VZc96mWMrspuVf+Esc+J1FRnHOTyVmt6ZKP8rh5KAaHLlX0k+0nL0gDpO5VNLH4dF+cu1KfBAjupfmaQoJFppeC5OcCWMFymp7TPMf3pXXsFAiLs3yGKbSGkVzGUSCCMbPYwmI82IChrYf98P4tQnZFxnPgSE0+YCk/YhXUzgZbk3G74ZPe/2P46q7GIJAOxJ7i+7xTJaOKKFYTT4Y/QUdlrjTB3oOCh8kjG+vtSMhC4CGub8tPBZGnbT0rc51VbUbcSxpONMK7ZDHYtFMwvzZ5H80b+2iiE8oWxWeowMIAM0t6ruw5/esDn/OCY8ksQLH605VDLfFWR2WFLcdcu/nuIOaZUnG9AILyInyZ0ZOa85YbA/wYkXxLTvsTWk09jw1Xl3XP7ZiU2OoroobqkGawBsurmWyWn3EC/0b5y0UqY5SEq1R9xeORPF11KMhen0UOIZeOoKjSU/X2PPxL1REzYFLoZvZyFVB8ApGdM+zkxgjcfbFCL/Ay/uQpqLj/ulRiuZXHkLxqOZESC9Dw/5zf5ysVAHrUSWwqWjWl4dipOFxfVFSYOtSf5vsqg//MlUpYFTO04H74adD+QZPxvtiKagSusOaUR5Fb88FQO1xxU8h0e0LvZCNTws9ojUHCBNoKSAEppQvRYqqN3o7Kr23wFtjEKRYCdbyn3/4aN+gGcrMWqrntQzjFPo+jsj7iCZkTkkEuq6PvK2fE1a71cEk0AWzI0gdqurPudduGX0a5GS/p5ekvbTX9Vr0nXRgTkTXfuFHToJQ91/HqpP2hPLdLG3ncWQmv/S9B998gjEDnihmxLhiop6ZquIkDD2Vp4gsEi2w1bM+3c5zS+UkkPGkg==

*/