function showSectors(district) {
    const sectorsSelection = document.getElementById("sectorsSelection");
    let sectors = `<option value="--" class="text-danger">--Choose district first--</option>`;

    // Check district and populate sectors accordingly
    if (district === "Huye") {
        sectors =
            `
    <option value="Gishamvu">Gishamvu</option>
    <option value="Karama">Karama</option>
    <option value="Kigoma">Kigoma</option>
    <option value="Kinazi">Kinazi</option>
    <option value="Maraba">Maraba</option>
    <option value="Mbazi">Mbazi</option>
    <option value="Mukura">Mukura</option>
    <option value="Ngoma">Ngoma</option>
    <option value="Ruhashya">Ruhashya</option>
    <option value="Huye">Huye</option>
    <option value="Rusatira">Rusatira</option>
    <option value="Rwaniro">Rwaniro</option>
    <option value="Simbi">Simbi</option>
    <option value="Tumba">Tumba</option>
    `;
    }
    else if (district === "Bugesera") {
        sectors =
            `
    <option value="Gashora">Gashora</option>
    <option value="Juru">Juru</option>
    <option value="Kamabuye">Kamabuye</option>
    <option value="Ntarama">Ntarama</option>
    <option value="Mareba">Mareba</option>
    <option value="Mayange">Mayange</option>
    <option value="Musenyi">Musenyi</option>
    <option value="Mwogo">Mwogo</option>
    <option value="Ngeruka">Ngeruka</option>
    <option value="Nyamata">Nyamata</option>
    <option value="Nyarugenge">Nyarugenge</option>
    <option value="Rilima">Rilima</option>
    <option value="Ruhuha">Ruhuha</option>
    <option value="Rweru">Rweru</option>
    <option value="Shyara">Shyara</option>
    `;
    }
    else if (district === "Rwamagana") {
        sectors =
            `<option value="Fumbwe">Fumbwe</option>
            <option value="Gahengeri">Gahengeri</option>
            <option value="Gishari">Gishari</option>
            <option value="Karenge">Karenge</option>
            <option value="Kigabiro">Kigabiro</option>
            <option value="Muhazi">Muhazi</option>
            <option value="Munyaga">Munyaga</option>
            <option value="Munyiginya">Munyiginya</option>
            <option value="Musha">Musha</option>
            <option value="Muyumbu">Muyumbu</option>
            <option value="Mwulire">Mwulire</option>
            <option value=" Rubona"> Rubona</option>
            <option value="Nzige">Nzige</option>
    `;
    }
    else if (district === "Nyaruguru") {
        sectors =
            `
            <option value="Busanze">Busanze</option>
            <option value="Cyahinda">Cyahinda</option>
            <option value="Kibeho">Kibeho</option>
            <option value="Kivu">Kivu</option>
            <option value="Mata">Mata</option>
            <option value="Muganza">Muganza</option>
            <option value="Munini">Munini</option>
            <option value="Ngera">Ngera</option>
            <option value="Ngoma">Ngoma</option>
            <option value="Nyabimata">Nyabimata</option>
            <option value="Nyagisozi">Nyagisozi</option>
            <option value="Ruheru">Ruheru</option>
            <option value="Ruramba">Ruramba</option>
            <option value="Rusenge">Rusenge</option>
    `;
    }
    else if (district === "Nyagatare") {
        sectors =
            `
            <option value="GATUNDA">GATUNDA</option>
            <option value="KARAMA">KARAMA</option>
            <option value="KARANGAZI">KARANGAZI</option>
            <option value="KATABAGEMU">KATABAGEMU</option>
            <option value="KIYOMBE">KIYOMBE</option>
            <option value="MATIMBA">MATIMBA</option>
            <option value="MIMURI">MIMURI</option>
            <option value="MUKAMA">MUKAMA</option>
            <option value="MUSHERI">MUSHERI</option>
            <option value="NYAGATARE">NYAGATARE</option>
            <option value="RUKOMO">RUKOMO</option>
            <option value="RWEMPASHA">RWEMPASHA</option>
            <option value="RWIMIYAGA">RWIMIYAGA</option>
            <option value="TABAGWE">TABAGWE</option>
    `;
    }
    else if (district === "Rusizi") {
        sectors =
            `<option value="Mururu">Mururu</option>
            <option value="Kamembe">Kamembe</option>
            <option value="Nyakarenzo">Nyakarenzo</option>
            <option value="Gitambi">Gitambi</option>
            <option value="Rwimbogo">Rwimbogo</option>
            <option value="Giheke">Giheke</option>
            <option value="Bweyeye">Bweyeye</option>
            <option value="Nyakabuye">Nyakabuye</option>
            <option value="Butare">Butare</option>
            <option value="Gikundamvura">Gikundamvura</option>
            <option value="Bugarama">Rugarama</option>
            <option value="Gashonga">Gashonga</option>
            <option value="Nkanka">Nkanka</option>
            <option value="Muganza">Nkombo</option>
            <option value="Nkombo">Nkombo</option>
            <option value="Nzahaha">Nzahaha</option>
            <option value="Nkungu">Nkungu</option>
    `;
    }
    else if (district === "Nyamasheke") {
        sectors =
            `<option value="Ruharambuga">Ruharambuga</option>
            <option value="Bushekeri">Bushekeri</option>
            <option value="Bushenge">Bushenge</option>
            <option value="Cyato">Cyato</option>
            <option value="Kagano">Kagano</option>
            <option value="Kanjongo">Kanjongo</option>
            <option value="Karambi">Karambi</option>
            <option value="Karengera">Karengera</option>
            <option value="Kirimbi">Kirimbi</option>
            <option value="Macuba">Macuba</option>
            <option value="Nyabitekeri">Nyabitekeri</option>
            <option value="Mahembe">Mahembe</option>
            <option value="Rangiro">Rangiro</option>
            <option value="Shangi">Shangi</option>
            <option value="Gihombo">Gihombo</option>
    `;
    }
    else if (district === "Rutsiro") {
        sectors =
            `<option value="Kivumu">Kivumu</option>
            <option value="Nyabirasi">Nyabirasi</option>
            <option value="Kigeyo">Kigeyo</option>
            <option value="Mushonyi">Mushonyi</option>
            <option value="Boneza">Boneza</option>
            <option value="Musasa">Musasa</option>
            <option value="Gihango">Gihango</option>
            <option value="Mushubati">Mushubati</option>
            <option value="Manihira">Manihira</option>
            <option value="Murunda">Murunda</option>
            <option value="Rusebeya">Rusebeya</option>
            <option value="Ruhango">Ruhango</option>
            <option value="Mukura">Mukura</option> 
    `;
    }
    else if (district === "Ngororero") {
        sectors =
            ` <option value="Bwira">Bwira</option>
            <option value="Gatumba">Gatumba,</option>
            <option value="Hindiro">Hindiro</option>
            <option value="Kabaya">Kabaya</option>
            <option value="Kageyo">Kageyo</option>
           <option value="Kavumu">Kavumu</option>
           <option value="Matyazo">Matyazo</option>
           <option value="Muhanda">Muhanda</option>
           <option value="Muhororo">Muhororo</option>
           <option value="Ndaro">Ndaro</option>
           <option value="Ngororero">Ngororero</option>
           <option value="Nyange">Nyange</option>
           <option value="Sovu">Sovu</option>
    `;
    }
    else if (district === "Karongi") {
        sectors =
            `<option value="Murambi">Murambi</option>
            <option value="Ruganda">Ruganda</option>
            <option value="Mutuntu">Mutuntu</option>
            <option value="Twumba">Twumba</option>
            <option value="Rwankuba">Rwankuba</option>
            <option value="Gashyita">Gashyita</option>
            <option value="Mubuga">Mubuga</option>
            <option value="Bwishyura">Bwishyura</option>
            <option value="Rubengera">Rubengera</option>
            <option value="Gitesi">Gitesi</option>
            <option value="Rugabano">Rugabano</option>
            <option value="Murundi">Murundi</option>
            <option value="Rugabano">Rugabano</option>
    `;
    }
    else if (district === "Rubavu") {
        sectors =
            `<option value="Bugeshi">Bugeshi</option>
            <option value="Mudende">Mudende</option>
            <option value="Kanzenze">Kanzenze</option>
            <option value="Kanama">Kanama</option>
            <option value="Nyundo">Nyundo</option>
            <option value="Nyamyumba">Nyamyumba</option>
            <option value="Gisenyi">Gisenyi</option>
            <option value="Rubavu">Rubavu</option>
            <option value="Nyakiriba">Nyakiriba</option>
            <option value="Cyanzarwe">Cyanzarwe</option>
            <option value="Busasamana">Busasamana</option>
            <option value="Rugerero">Rugerero</option>
    `;
    }
    else if (district === "Nyabihu") {
        sectors =
            `<option value="Mukamira">Mukamira</option>
            <option value="Jenda">Jenda</option>
            <option value="Bigogwe">Bigogwe</option>
            <option value="Kabatwa">Kabatwa</option>
            <option value="Karago">Karago</option>
            <option value="Rambura">Rambura</option>
            <option value="Jomba">Jomba</option>
            <option value="Rurembo">Rurembo</option>
            <option value="Mulinga">Mulinga"</option>
            <option value="Kintobo">Kintobo</option>
            <option value="Rugera">Rugera</option>
            <option value="Shyira">Shyira</option>
    `;
    }
    else if (district === "Rulindo") {
        sectors =
            `<option value="Shyorongi">Shyorongi</option>
            <option value="Rusiga">Rusiga</option>
            <option value="Ngoma">Ngoma</option>
            <option value="Murambi">Murambi</option>
            <option value="Masoro">Masoro</option>
            <option value="Ntarabana">Ntarabana</option>
            <option value="Cyinzuzi">Cyinzuzi</option>
            <option value="Mbogo">Mbogo</option>
            <option value="Burega">Burega</option>
            <option value="Tumba">Tumba</option>
            <option value="Bushyoki">Bushyoki</option>
            <option value="Kinihira">Kinihira</option>
            <option value="Kisaro">Kisaro</option>
            <option value="Rukozo">Rukozo</option>
            <option value="Base">Base</option>
            <option value="Cyungo">Cyungo</option>
            <option value="Tumba">Tumba</option>
    `;
    }
    else if (district === "Musanze") {
        sectors =
            `<option value="Busogo">Busogo</option>
            <option value="Cyuve">Cyuve</option>
            <option value="Gacaca">Gacaca</option>
            <option value="Gashaki">Gashaki</option>
            <option value="Gataraga">Gataraga</option>
            <option value="Kimonyi">Kimonyi</option>
            <option value="Kinigi">Kinigi</option>
            <option value="Muhoza">Muhoza</option>
            <option value="Muko">Muko</option>
            <option value="Musanze">Musanze</option>
            <option value="Nkotsi">Nkotsi</option>
            <option value="Nyange">Nyange</option>
            <option value="Remera">Remera</option>
            <option value="Rwaza">Rwaza</option>
            <option value="Shingiro">Shingiro</option>
    `;
    }
    else if (district === "Gicumbi") {
        sectors =
            `<option value="Bukure">Bukure</option>
            <option value="Bwisige">Bwisige</option>
            <option value="Byumba">Byumba</option>
            <option value="Cyumba">Cyumba</option>
            <option value="Giti">Giti</option>
            <option value="Kaniga">Kaniga</option>
            <option value="Manyagiro">Manyagiro</option>
            <option value="Miyove">Miyove</option>
            <option value="Kageyo">Kageyo</option>
            <option value="Mukarange">Mukarange</option>
            <option value="Muko">Muko</option>
            <option value="Mutete">Mutete</option>
            <option value="Nyamiyaga">Nyamiyaga</option>
            <option value="Nyankenke II">Nyankenke II</option>
            <option value="Rubaya">Rubaya</option>
            <option value="Rukomo">Rukomo</option>
            <option value="Rushaki">Rushaki</option>
            <option value="Rutare">Rutare</option>
            <option value="Ruvune">Ruvune</option>
            <option value="Rwamiko">Rwamiko</option>
            <option value="Shyangasha">Shyangasha</option>
    `;
    }
    else if (district === "Gakenke") {
        sectors =
            `<option value="Busengo">Busengo</option>
            <option value="Coko">Coko</option>
            <option value="Cyabingo">Cyabingo</option>
            <option value="Gakenke">Gakenke</option>
            <option value="Gashenyi">Gashenyi</option>
            <option value="Mugunga">Mugunga</option>
            <option value="Janja">Janja</option>
            <option value="Kamubuga">Kamubuga</option>
            <option value="Karambo">Karambo</option>
            <option value="Kivuruga">Kivuruga</option>
            <option value="Mataba">Mataba</option>
            <option value="Minazi">Minazi</option>
            <option value="Muhondo">Muhondo</option>
            <option value="Muyongwe">Muyongwe</option>
            <option value="Muzo">Muzo</option>
            <option value="Nemba">Nemba</option>
            <option value="Ruli">Ruli</option>
            <option value="Rusasa">Rusasa</option>
            <option value="Rushashi">Rushashi</option>
    `;
    }
    else if (district === "Burera") {
        sectors =
            `<option value="Bungwe">Bungwe</option>
            <option value="Butaro">Butaro</option>
            <option value="Cyanika">Cyanika</option>
            <option value="Cyeru">Cyeru</option>
            <option value="Gahunga">Gahunga</option>
            <option value="Gatebe">Gatebe</option>
            <option value="Gitovu">Gitovu</option>
            <option value="Kagogo">Kagogo</option>
            <option value="Kinoni">Kinoni</option>
            <option value="Kinyababa">Kinyababa</option>
            <option value=" Kivuye"> Kivuye</option>
            <option value="Nemba">Nemba</option>
            <option value="Rugarama">Rugarama</option>
            <option value="Rugendabari">Rugendabari</option>
            <option value=" Ruhunde"> Ruhunde</option>
            <option value="Rusarabuye">Rusarabuye</option>
            <option value="Rwerere">Rwerere</option>
    `;
    }
    else if (district === "Ruhango") {
        sectors =
            `<option value=" Kinazi"> Kinazi</option>
            <option value="Byimana">Byimana</option>
            <option value="Bweramana">Bweramana</option>
            <option value="Mbuye">Mbuye</option>
            <option value="Ruhango">Ruhango</option>
            <option value="Mwendo">Mwendo</option>
            <option value="Kinihira">Kinihira</option>
            <option value="Ntongwe">Ntongwe</option>
            <option value="Kabagari">Kabagari</option>
    `;
    }
    else if (district === "Nyanza") {
        sectors =
            `<option value="Mukingo">Mukingo</option>
            <option value="Cyabakamyi">Cyabakamyi</option>
            <option value="Rwabicuma">Rwabicuma</option>
            <option value="Nyagisozi">Nyagisozi</option>
            <option value="Muyira">Muyira</option>
            <option value="Kagoma">Kagoma</option>
            <option value="Busoro">Busoro</option>
            <option value="Kibirizi">Kibirizi</option>
            <option value="Ntyazo">Ntyazo</option>
    `;
    }
    else if (district === "Nyamagabe") {
        sectors =
            `<option value="Buruhukiro">Buruhukiro</option>
            <option value=" Cyanika"> Cyanika</option>
            <option value="Gatare">Gatare</option>
            <option value="Kaduha">Kaduha</option>
            <option value="Kamegeli">Kamegeli</option>
            <option value="Kibirizi">Kibirizi</option>
            <option value="Kibumbwe">Kibumbwe</option>
            <option value="Kitabi">Kitabi</option>
            <option value="Mbazi">Mbazi</option>
            <option value="Mugano">Mugano</option>
            <option value="Musange">Musange</option>
            <option value="Musebeya">Musebeya</option>
            <option value="Mushubi">Mushubi</option>
            <option value="Nkomane">Nkomane</option>
            <option value="Gasaka">Gasaka</option>
            <option value="Tare">Tare</option>
            <option value="Uwinkingi">Uwinkingi</option>
    `;
    }
    else if (district === "Muhanga") {
        sectors =
            `<option value="Nyabinoni">Nyabinoni</option>
            <option value="Rongi">Rongi</option>
            <option value="Kibangu">Kibangu</option>
            <option value="Kiyumba">Kiyumba</option>
            <option value="Kabacuzi">Kabacuzi</option>
            <option value="Rugendabar">Rugendabar</option>
            <option value="Muhanga">Muhanga</option>
            <option value="Mushishiro">Mushishiro</option>
            <option value="Nyarusange">Nyarusange</option>
            <option value="Nyamabuye">Nyamabuye</option>
            <option value="Shyogwe">Shyogwe</option>
            <option value="Cyeza">Cyeza</option>
    `;
    }
    else if (district === "Kamonyi") {
        sectors =
            `<option value="Gacurabwenge">Gacurabwenge</option>
            <option value="Karama">Karama</option>
            <option value="Kayenzi">Kayenzi</option>
            <option value="Kayumbu">Kayumbu</option>
            <option value="Mugina">Mugina</option>
            <option value="Musambira">Musambira</option>
            <option value="Ngamba">Ngamba</option>
            <option value="Nyamiyaga">Nyamiyaga</option>
            <option value="Nyarubaka">Nyarubaka</option>
            <option value="Rugalika">Rugalika</option>
            <option value="Rukoma ">Rukoma </option>
            <option value="Runda">Runda</option>
    `;
    }
    else if (district === "Gisagara") {
        sectors =
            `<option value="Gikinko">Gikinko</option>
            <option value="Gishubi">Gishubi</option>
            <option value="Kansi">Kansi</option>
            <option value="Kibirizi">Kibirizi</option>
            <option value="Kigembe">Kigembe</option>
            <option value="Mamba">Mamba</option>
            <option value="Muganza">Muganza</option>
            <option value="Mukindo">Mukindo</option>
            <option value="Mushya">Mushya</option>
            <option value="Ndora">Ndora</option>
            <option value="Nyanza">Nyanza</option>
            <option value="Save">Save</option>
            <option value="Mugombwa">Mugombwa</option>
    `;
    }
    else if (district === "Ngoma") {
        sectors =
            `<option value="GASHANDA">GASHANDA</option>
            <option value="JARAMA">JARAMA</option>
            <option value="KAREMBO">KAREMBO</option>
            <option value="KAZO">KAZO</option>
            <option value="KIBUNGO">KIBUNGO</option>
            <option value="MUGESERA">MUGESERA</option>
            <option value="MURAMA">MURAMA</option>
            <option value=" MUTENDERI"> MUTENDERI</option>
            <option value="REMERA">REMERA</option>
            <option value="RUKIRA">RUKIRA</option>
            <option value="RUKUMBERI">RUKUMBERI</option>
            <option value="RERENGE">RERENGE</option>
            <option value=" SAKE"> SAKE</option>
            <option value="ZAZA">ZAZA</option>
    `;
    }
    else if (district === "Kirehe") {
        sectors =
            `<option value="Gahara">Gahara</option>
            <option value="Gatore">Gatore</option>
            <option value="Kigarama">Kigarama</option>
            <option value="Kigina">Kigina</option>
            <option value="Kirehe">Kirehe</option>
            <option value="Mahama">Mahama</option>
            <option value="Mpanga">Mpanga</option>
            <option value="Mpanga">Mpanga</option>
            <option value="Mushikiri">Mushikiri</option>
            <option value="Nasho">Nasho</option>
            <option value="Nyamugari">Nyamugari</option>
            <option value="Nyarubuye">Nyarubuye</option>
    `;
    }
    else if (district === "Kayonza") {
        sectors =
            `<option value="Gahini">Gahini</option>
            <option value="Kabarondo">Kabarondo</option>
            <option value="Mukarange">Mukarange</option>
            <option value="Murama">Murama</option>
            <option value="Murundi">Murundi</option>
            <option value="Mwiri">Mwiri</option>
            <option value="Ndego">Ndego</option>
            <option value="Nyamirama">Nyamirama</option>
            <option value="Rukara">Rukara</option>
            <option value="Ruramira">Ruramira</option>
            <option value="Rwinkwavu">Rwinkwavu</option>
    `;
    }
    else if (district === "Gatsibo") {
        sectors =
            ` <option value=" Gasange"> Gasange</option>
            <option value="Gatsibo">Gatsibo</option>
            <option value="Gitoki">Gitoki</option>
            <option value="Kabarore">Kabarore</option>
            <option value="Kageyo">Kageyo</option>
            <option value="Kiramuruzi">Kiramuruzi</option>
            <option value="Kiziguro">Kiziguro</option>
            <option value="Muhura">Muhura</option>
            <option value="Murambi">Murambi</option>
            <option value=" Ngarama"> Ngarama</option>
            <option value="Nyagihanga">Nyagihanga</option>
            <option value="Rugarama">Rugarama</option>
            <option value="Rwimbogo">Rwimbogo</option>
            <option value="Remera">Remera</option>
    `;
    }
    else if (district === "Bugesera") {
        sectors =
            `<option value="Gashora">Gashora</option>
            <option value="Juru">Juru</option>
            <option value="Kamabuye">Kamabuye</option>
            <option value="Ntarama">Ntarama</option>
            <option value="Mareba">Mareba</option>
            <option value="Mayange">Mayange</option>
            <option value="Musenyi">Musenyi</option>
            <option value="Mwogo">Mwogo</option>
            <option value="Ngeruka">Ngeruka</option>
            <option value="Nyamata">Nyamata</option>
            <option value="Nyarugenge">Nyarugenge</option>
            <option value="Rilima">Rilima</option>
            <option value="Ruhuha">Ruhuha</option>
            <option value="Rweru">Rweru</option>
            <option value="Shyara">Shyara</option>
    `;
    }
    else if (district === "Nyarugenge") {
        sectors =
            `
            <option value="Gitega">Gitega</option>
            <option value="Kanyinya">Kanyinya</option>
            <option value="Kigali">Kigali</option>
            <option value="Kimisagara">Kimisagara</option>
            <option value="Mageregere">Mageregere</option>
            <option value="Muhima">Muhima</option>
            <option value="Nyakabanda">Nyakabanda</option>
            <option value="Nyamirambo">Nyamirambo</option>
            <option value="Nyarugenge">Nyarugenge</option>
            <option value="Rwezamenyo">Rwezamenyo</option>
    `;
    }
    else if (district === "Kicukiro") {
        sectors = `
        <option value="Niboyi">Niboyi</option>
        <option value="Gahanga">Gahanga</option>
        <option value="Gatenga">Gatenga</option>
        <option value="Gikondo">Gikondo</option>
        <option value="Kanombe">Kanombe</option>
        <option value="Kagarama">Kagarama</option>
        <option value="Kigarama">Kigarama</option>
        <option value="Kicukiro">Kicukiro</option>
        <option value="Masaka">Masaka</option>
        <option value="Nyarugunga">Nyarugunga</option>
    `;
    }
    else if (district === "Gasabo") {
        sectors =
            `
            <option value="Jabana">Jabana</option>
            <option value="Kacyiru">Kacyiru</option>
            <option value="Gatsata">Gastasa</option>
            <option value="Bumbogo">Bumbogo</option>
            <option value="Gikomero">Gikomero</option>
            <option value="Gisozi">Gisozi</option>
            <option value="Jali">Jali</option>
            <option value="Kimihurura">Kimihurura</option>
            <option value="Kimironko">Kimironko</option> 
            <option value="Kinyinya">Kinyinya</option>
            <option value="Ndera">Ndera</option>
            <option value="Nduba">Nduba</option>
            <option value="Remera">Remera</option>
            <option value="Rusororo">Rusororo</option>
            <option value="Rutunga">Rutunga</option>
    `;
    }

    sectorsSelection.innerHTML = sectors;

}


function showCells(sector) {
    const cellsSelection = document.getElementById("cellsSelection");
    let cells = `<option value="--" class="text-danger">--Choose Sector first--</option>`;
    if (sector === "Gashora") {
        cells =
            `
    <option value="Gishamvu">Gishamvu</option>
    <option value="Karama">Karama</option>
    <option value="Kigoma">Kigoma</option>
    <option value="Kinazi">Kinazi</option>
    <option value="Maraba">Maraba</option>
    <option value="Mbazi">Mbazi</option>
    <option value="Mukura">Mukura</option>
    <option value="Ngoma">Ngoma</option>
    <option value="Ruhashya">Ruhashya</option>
    <option value="Huye">Huye</option>
    <option value="Rusatira">Rusatira</option>
    <option value="Rwaniro">Rwaniro</option>
    <option value="Simbi">Simbi</option>
    <option value="Tumba">Tumba</option>
    `;
    }
    else if (sector === "Gishamvu") {
        cells =
            `
    <option value="Gashora">Gashora</option>
    <option value="Juru">Juru</option>
    <option value="Kamabuye">Kamabuye</option>
    <option value="Ntarama">Ntarama</option>
    <option value="Mareba">Mareba</option>
    <option value="Mayange">Mayange</option>
    <option value="Musenyi">Musenyi</option>
    <option value="Mwogo">Mwogo</option>
    <option value="Ngeruka">Ngeruka</option>
    <option value="Nyamata">Nyamata</option>
    <option value="Nyarugenge">Nyarugenge</option>
    <option value="Rilima">Rilima</option>
    <option value="Ruhuha">Ruhuha</option>
    <option value="Rweru">Rweru</option>
    <option value="Shyara">Shyara</option>
    `;
    }

    cellsSelection.innerHTML = cells;
}

function showVillages(cell) {
    // Get the village selection dropdown element
    const villagesSelection = document.getElementById("villagesSelection");

    // Default message if no cell is selected or if no villages available
    let villages = `<option value="--" class="text-danger">--Choose Cell first--</option>`;

    // Check which cell was selected and update the village options accordingly
    if (cell === "Mareba") {
        villages =
            `
        <option value="Rupango">Rupango</option>
        <option value="Village1">Village1</option>
        <option value="Village2">Village2</option>
        <option value="Village3">Village3</option>
        `;
    } else if (cell === "Shyara") {
        villages =
            `
        <option value="VillageA">VillageA</option>
        <option value="VillageB">VillageB</option>
        <option value="VillageC">VillageC</option>
        `;
    } else {
        villages =
            `
        <option value="--">No villages available</option>
        `;
    }

    // Update the inner HTML of the villages dropdown
    villagesSelection.innerHTML = villages;
}

