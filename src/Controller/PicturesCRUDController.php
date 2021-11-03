<?php

namespace App\Controller;
include '../public/cryptage.php';

use App\Entity\Pictures;
use App\Form\PicturesType;
use App\Repository\PicturesRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/pictures/c/r/u/d")
 */

class PicturesCRUDController extends AbstractController
{
    private Security $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
        // if ($this->security->getUser()) {
        //     return $this->redirectToRoute('/');
        // }
    }
    /**
     * @Route("/", name="pictures_c_r_u_d_index", methods={"GET"})
     */
    public function index(PicturesRepository $picturesRepository): Response
    {
        return $this->render('pictures_crud/index.html.twig', [
            'pictures' => $picturesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="pictures_c_r_u_d_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $picture = new Pictures();
        $form = $this->createForm(PicturesType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('imageFile')->getData();
            // //get image file
            // $RSA = new RSA_Handler();

            // $keys = $RSA->generate_keypair(1024);
            // $encrypted = $RSA->encrypt($image, $keys);
            // $decrypted = $RSA->decrypt($encrypted, $keys[1]);

            $entityManager = $this->getDoctrine()->getManager();
            ///get image file

            $content = file_get_contents($image);
            $image = base64_encode($content);
            $picture->setDateUpload(new \DateTime());
            $user = $this->security->getUser();
            $picture->setUser($user);
            $picture->setFile($image);
            $entityManager->persist($picture);
            $entityManager->flush();

            return $this->redirectToRoute(
                'pictures_c_r_u_d_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('pictures_crud/new.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="pictures_c_r_u_d_show", methods={"GET"})
     */
    public function show(Pictures $picture): Response
    {
        return $this->render('pictures_crud/show.html.twig', [
            'picture' => $picture,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pictures_c_r_u_d_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Pictures $picture): Response
    {
        $form = $this->createForm(PicturesType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()
                ->getManager()
                ->flush();

            return $this->redirectToRoute(
                'pictures_c_r_u_d_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('pictures_crud/edit.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="pictures_c_r_u_d_delete", methods={"POST"})
     */
    public function delete(Request $request, Pictures $picture): Response
    {
        if (
            $this->isCsrfTokenValid(
                'delete' . $picture->getId(),
                $request->request->get('_token')
            )
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($picture);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'pictures_c_r_u_d_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}

class RSA_Handler
{
    function encrypt($text, $key)
    {
        list($p, $r, $keysize) = unserialize(base64_decode($key));
        $in = $this->blockify($text, $keysize);
        $out = '';
        foreach ($in as $block) {
            if ($block) {
                $cryptblock = $this->crypt_num($this->txt2num($block), $p, $r);
                $out .= $this->long_base_convert($cryptblock, 10, 92) . ' ';
            }
        }
        return $out;
    }

    function decrypt($code, $key)
    {
        list($q, $r) = unserialize(base64_decode($key));
        $in = explode(' ', $code);
        $out = '';
        foreach ($in as $block) {
            if ($block) {
                $block = $this->long_base_convert($block, 92, 10);
                $out .= $this->num2txt($this->crypt_num($block, $q, $r));
            }
        }
        return $out;
    }

    function generate_keypair($bits = 1024)
    {
        $km = new RSA_keymaker();
        $keys = $km->make_keys($bits);
        //The keys are separated into arrays and then serialized and encoded in base64
        //This makes it easier to store and transmit them
        //
        //The private key should probably be encrypted with a user-supplied key (in AES or DES3)...
        //This way it can be stored on the server, yet still be secure. The user-supplied key should not be stored.
        $pub = base64_encode(serialize([$keys[0], $keys[2], $bits]));
        $priv = base64_encode(serialize([$keys[1], $keys[2], $bits]));
        return [$pub, $priv];
    }

    function crypt_num($num, $key, $mod)
    {
        //The powerhorse function. This is where the encryption/decryption actually happens.
        //This function is used whether you are encrypting or decrypting.
        return $this->powmod($num, $key, $mod);
    }

    function long_base_convert($numstring, $frombase, $tobase)
    {
        //Converts a long integer (passed as a string) to/from any base from 2 to 92
        $chars =
            "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-+=!@#$%^*(){[}]|:,.?/`~&';\"\\";
        $fromstring = substr($chars, 0, $frombase);
        $tostring = substr($chars, 0, $tobase);

        $length = strlen($numstring);
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $number[$i] = strpos($fromstring, $numstring[$i]);
        }
        do {
            $divide = 0;
            $newlen = 0;
            for ($i = 0; $i < $length; $i++) {
                $divide = $divide * $frombase + $number[$i];
                if ($divide >= $tobase) {
                    $number[$newlen++] = (int) ($divide / $tobase);
                    $divide = $divide % $tobase;
                } elseif ($newlen > 0) {
                    $number[$newlen++] = 0;
                }
            }
            $length = $newlen;
            $result = $tostring[$divide] . $result;
        } while ($newlen != 0);
        return $result;
    }

    function blockify($in, $keysize)
    {
        //Calculate blocksize by keysize
        $b_len = floor($keysize / 8);
        return str_split($in, $b_len);
    }

    function txt2num($str)
    {
        //Turns regular text into a number that can be manipulated by the RSA algorithm
        $result = '0';
        $n = strlen($str);
        do {
            $result = bcadd(bcmul($result, '256'), ord($str[--$n]));
        } while ($n > 0);
        return $result;
    }

    function num2txt($num)
    {
        //Turns the numeric representation of text (as output by txt2num) back into text
        $result = '';
        do {
            $result .= chr(bcmod($num, '256'));
            $num = bcdiv($num, '256');
        } while (bccomp($num, '0'));
        return $result;
    }

    function powmod($num, $pow, $mod)
    {
        if (function_exists('bcpowmod')) {
            // bcpowmod is only available under PHP5
            return bcpowmod($num, $pow, $mod);
        }

        // emulate bcpowmod
        $result = '1';
        do {
            if (!bccomp(bcmod($pow, '2'), '1')) {
                $result = bcmod(bcmul($result, $num), $mod);
            }
            $num = bcmod(bcpow($num, '2'), $mod);

            $pow = bcdiv($pow, '2');
        } while (bccomp($pow, '0'));
        return $result;
    }
}

class RSA_keymaker
{
    static $primes = null;

    function __construct()
    {
        if (is_null($this->primes)) {
            //Make $this->primes an array of all primes under 20,000
            //We will use this list to rule out the "easy" composite (non-prime) numbers

            for ($i = 0; $i < 20000; $i++) {
                $numbers[] = $i;
            }
            $numbers[0] = $numbers[1] = 0; //Zero and one are not primes :)
            foreach ($numbers as $i => $num) {
                if (!$num) {
                    continue;
                }
                $j = $i;

                for ($j += $i; $j < 20000; $j += $i) {
                    //Jump to each multiple of the current number and set it to 0 (not prime)
                    $numbers[$j] = 0;
                }
            }
            foreach ($numbers as $num) {
                //Take all the prime numbers and fill the primes array
                if ($num) {
                    $this->primes[] = $num;
                }
            }
        }
    }

    function make_keys($bits = 1024, $u = false, $v = false)
    {
        //If not provided, select 2 random prime numbers each at about half the bit size of our key
        //We keep a possible variant of 2 bits so that there are a wider range of primes that can be used
        $variant = rand(0, 2);
        if (!$u) {
            $u = $this->make_prime(ceil($bits / 2) + $variant);
        }
        if (!$v) {
            $v = $this->make_prime(floor($bits / 2) - $variant);
        }
        while (
            substr($u, -16, 2) < substr($v, -16, 2) + 2 &&
            substr($u, -16, 2) > substr($v, -16, 2) - 2
        ) {
            //Make sure the 2 primes are at least 1 quadrill
        }

        //Find our modulo r and phi(r)
        $r = bcmul($u, $v);
        $phir = bcmul(bcsub($u, 1), bcsub($v, 1));

        //Pick a value for p (The Public key). We will make it 17 bits or smaller.
        $psize = $bits > 51 ? 17 : intval($bits / 3);
        $p = $this->make_prime($psize);

        //Find the inverse of p mod phi(r) using the Extended Euclidian Algorithm
        $q = $this->euclid($p, $phir);

        return [$p, $q, $r];
    }

    function make_prime($bits)
    {
        //This function should not be used to generate primes less than 18 bits

        $min = bcpow(2, $bits - 1);
        $max = bcsub(bcmul($min, 2), 1);
        $digits = strlen($max);
        while (strlen($min) < $digits) {
            $min = '0' . $min;
        }
        $ent = $this->entropyarray($digits);
        $maxed = true;
        $mined = true;
        $num = '';
        for ($i = 0; $i < $digits; $i++) {
            //Create a long integer between $min and $max starting with the entropy number
            $thismax = 9;
            $thismin = 0;
            if ($maxed) {
                $thismax = substr($max, $i, 1);
            }
            if ($mined) {
                $thismin = substr($min, $i, 1);
            }

            //Add random numbers (mod 10) until the number meets the constraints
            $thisdigit = ($ent[$i] + rand(0, 9)) % 10;
            if ($i == $digits - 1) {
                //The last digit should be a 1, 3, 7 or 9
                while (
                    $thisdigit != 1 &&
                    $thisdigit != 3 &&
                    $thisdigit != 7 &&
                    $thisdigit != 9 &&
                    $thisdigit <= $thismax &&
                    $thisdigit >= $thismin
                ) {
                    $thisdigit = ($thisdigit + rand(0, 9)) % 10;
                }
            } else {
                while ($thisdigit <= $thismax && $thisdigit >= $thismin) {
                    $thisdigit = ($thisdigit + rand(0, 9)) % 10;
                }
            }
            $num .= $thisdigit;
            if ($maxed && $thisdigit < $thismax) {
                $maxed = false;
            }
            if ($mined && $thisdigit > $thismin) {
                $mined = false;
            }
        }

        //Check if the number is prime
        while (!$this->is_prime($num)) {
            //If the number is not prime, add 2 or 4 (since it is currently an odd number)
            //This will keep the number odd and skip 5 to speed up the primality testing
            if (substr($num, -1, 1) == 3) {
                $num = bcadd($num, 4);
            } else {
                $num = bcadd($num, 2);
            }
        }
        return $num;
    }

    function entropyarray($digits)
    {
        //create a long number based on as much entropy as possible
        $a = base_convert(md5(microtime()), 16, 10);
        $b = base_convert(sha1(@exec('uptime')), 16, 10);
        $c = mt_rand();
        $d = disk_total_space('/');
        $e = rand();
        $f = memory_get_usage();

        //Make sure it is only numbers, scramble it and make it the right length
        $num = str_shuffle(preg_replace('[^0-9]', '', $a . $b . $c . $d . $e));
        if (strlen($num) > $digits) {
            $num = substr($num, 0, $digits);
        } else {
            while (strlen($num) < $digits) {
                $num = str_shuffle(
                    substr(base_convert(md5($num), 16, 10), 3, 1) . $num
                );
            }
        }

        //Turn the number into an array and return it
        $ent_array = str_split($num);
        return $ent_array;
    }

    function is_prime($num)
    {
        if (bccomp($num, 1) < 1) {
            return false;
        }
        //Clear the easy stuff (divide by all primes under 20,000)
        foreach ($this->primes as $prime) {
            if (bccomp($num, $prime) == 0) {
                return true;
            }
            if (!bcmod($num, $prime)) {
                return false;
            }
        }

        //Try the more complex method with the first 7 primes as bases
        for ($i = 0; $i < 7; $i++) {
            if (!$this->_millerTest($num, $this->primes[$i])) {
                return false;
            } //Number is composite
        }

        //Strong probability that the number is prime
        return true;
    }

    function _millerTest($num, $base)
    {
        if (!bccomp($num, '1')) {
            // 1 is not prime ;)
            return false;
        }
        $tmp = bcsub($num, '1');

        $zero_bits = 0;
        while (!bccomp(bcmod($tmp, '2'), '0')) {
            $zero_bits++;
            $tmp = bcdiv($tmp, '2');
        }

        $tmp = $this->powmod($base, $tmp, $num);
        if (!bccomp($tmp, '1')) {
            // $num is probably prime
            return true;
        }

        while ($zero_bits--) {
            if (!bccomp(bcadd($tmp, '1'), $num)) {
                // $num is probably prime
                return true;
            }
            $tmp = $this->powmod($tmp, '2', $num);
        }
        // $num is composite
        return false;
    }

    function euclid($num, $mod)
    {
        //The Extended Euclidian Algorithm
        $x = '1';
        $y = '0';
        $num1 = $mod;
        do {
            $tmp = bcmod($num, $num1);
            $q = bcdiv($num, $num1);
            $num = $num1;
            $num1 = $tmp;

            $tmp = bcsub($x, bcmul($y, $q));
            $x = $y;
            $y = $tmp;
        } while (bccomp($num1, '0'));
        if (bccomp($x, '0') < 0) {
            $x = bcadd($x, $mod);
        }
        return $x;
    }

    function powmod($num, $pow, $mod)
    {
        if (function_exists('bcpowmod')) {
            // bcpowmod is only available under PHP5
            return bcpowmod($num, $pow, $mod);
        }

        // emulate bcpowmod
        $result = '1';
        do {
            if (!bccomp(bcmod($pow, '2'), '1')) {
                $result = bcmod(bcmul($result, $num), $mod);
            }
            $num = bcmod(bcpow($num, '2'), $mod);

            $pow = bcdiv($pow, '2');
        } while (bccomp($pow, '0'));
        return $result;
    }
}
