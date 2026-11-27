(function () {
    function ambilElemen(id) {
        return document.getElementById(id);
    }

    function perbaruiCountdown() {
        var spans = document.getElementsByTagName("span");
        var sekarang = new Date().getTime();
        for (var i = 0; i < spans.length; i++) {
            var target = spans[i].getAttribute("data-dl");
            if (!target) {
                continue;
            }
            var sisaDetik = Math.floor((parseInt(target, 10) - sekarang) / 1000);
            if (sisaDetik <= 0) {
                spans[i].innerHTML = "Lewat";
            } else {
                var hari = Math.floor(sisaDetik / 86400);
                var jam  = Math.floor((sisaDetik % 86400) / 3600);
                var mnt  = Math.floor((sisaDetik % 3600) / 60);
                var dtk  = sisaDetik % 60;
                spans[i].innerHTML = hari + "h " + jam + "j " + mnt + "m " + dtk + "d";
            }
        }
    }

    perbaruiCountdown();
    setInterval(perbaruiCountdown, 1000);

    document.onclick = function (ev) {
        var eventObj = ev || window.event;
        var target = eventObj.target || eventObj.srcElement;
        var kelas = target && target.className ? String(target.className) : "";
        if (kelas.indexOf("del") > -1 && !confirm("Yakin menghapus data ini?")) {
            return false;
        }
    };

    var inputFilter = ambilElemen("filter");
    if (inputFilter) {
        inputFilter.onkeyup = function () {
            var kataKunci = inputFilter.value.toLowerCase();
            var baris = document.getElementsByTagName("tr");
            var tautan = document.getElementsByTagName("a");

            for (var i = 0; i < baris.length; i++) {
                if (baris[i].className.indexOf("row") > -1) {
                    var cocokBaris = baris[i].innerHTML.toLowerCase().indexOf(kataKunci) > -1;
                    baris[i].style.display = cocokBaris ? "" : "none";
                }
            }
            for (var j = 0; j < tautan.length; j++) {
                if (tautan[j].className.indexOf("dl-item") > -1) {
                    var cocokItem = tautan[j].innerHTML.toLowerCase().indexOf(kataKunci) > -1;
                    tautan[j].style.display = cocokItem ? "" : "none";
                }
            }
        };
    }

    window.openM = function () {
        var modalOv = ambilElemen("ov");
        if (modalOv) {
            modalOv.className = "ov show";
        }
    };

    window.closeM = function () {
        var modalOv = ambilElemen("ov");
        if (modalOv) {
            modalOv.className = "ov";
        }
    };

    window.tab = function (nomorTab) {
        ambilElemen("t1").style.display = (nomorTab == 1) ? "block" : "none";
        ambilElemen("t2").style.display = (nomorTab == 2) ? "block" : "none";
        ambilElemen("k1").className = (nomorTab == 1) ? "on" : "";
        ambilElemen("k2").className = (nomorTab == 2) ? "on" : "";
    };

    var semuaBaris = document.getElementsByTagName("tr");
    var kartuDetail = ambilElemen("detail");
    if (kartuDetail) {
        for (var i = 0; i < semuaBaris.length; i++) {
            (function (barisAktif) {
                if (barisAktif.className.indexOf("row") < 0) {
                    return;
                }
                barisAktif.onclick = function () {
                    for (var j = 0; j < semuaBaris.length; j++) {
                        semuaBaris[j].className = semuaBaris[j].className.replace(" hl", "");
                    }
                    barisAktif.className += " hl";

                    function ambilData(atribut) {
                        return barisAktif.getAttribute("data-" + atribut) || "-";
                    }

                    ambilElemen("d_id").innerHTML = ambilData("id");
                    ambilElemen("d_av").innerHTML = ambilData("ini");
                    ambilElemen("d_nama").innerHTML = ambilData("nama");
                    ambilElemen("d_sub").innerHTML = ambilData("id");
                    ambilElemen("d_fak").innerHTML = ambilData("fak");
                    ambilElemen("d_prodi").innerHTML = ambilData("prodi");
                    ambilElemen("d_umur").innerHTML = ambilData("umur");
                    ambilElemen("d_gen").innerHTML = ambilData("gen");
                    ambilElemen("d_edit").href = ambilData("edit");
                    ambilElemen("d_i").value = ambilData("pk");
                    kartuDetail.style.display = "block";
                };
            })(semuaBaris[i]);
        }
    }

    window.ed = function (idField) {
        var field = ambilElemen(idField);
        if (!field) {
            return;
        }
        field.removeAttribute("readonly");
        field.className = "edit";
        field.focus();
        var tombolSimpan = ambilElemen("sv");
        if (tombolSimpan) {
            tombolSimpan.style.display = "inline-block";
        }
    };
})();