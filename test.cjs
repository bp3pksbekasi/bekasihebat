const fs = require('fs');

function toTitleCase(value) {
    if (!value) return '';
    return String(value).split(' ').map(word => {
        return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
    }).join(' ');
}

function hydrateCalegPayload(payload) {
    const dataset = { dapils: new Map(), totalRows: payload.totalRows || 0, allPartyNames: new Set(payload.allPartyNames || []) };
    
    (payload.dapils || []).forEach(dapilData => {
        const dapilObj = {
            dapil: dapilData.dapil,
            totalSuara: dapilData.totalSuara || 0,
            calegMap: new Map(),
            partyMap: new Map(),
            villagePartyMap: new Map(),
            rwPartyMap: new Map(),
        };

        (dapilData.calegs || []).forEach(caleg => {
            caleg.dapil = dapilData.dapil;
            
            const newDesaMap = new Map();
            Object.entries(caleg.desaMap || {}).forEach(([key, suara]) => {
                const parts = String(key).split('__');
                const kec = parts[1] || '';
                const desa = parts[2] || parts[0] || '';
                newDesaMap.set(key, { desa: toTitleCase(desa), kecamatan: toTitleCase(kec), suara: Number(suara) });
            });
            caleg.desaMap = newDesaMap;
            
            const newKecMap = new Map();
            Object.entries(caleg.kecamatanMap || {}).forEach(([key, suara]) => {
                newKecMap.set(key, { kecamatan: toTitleCase(key), suara: Number(suara) });
            });
            caleg.kecamatanMap = newKecMap;
            
            caleg.rwMap = new Map();
            caleg.tpsSet = new Set();
            
            dapilObj.calegMap.set(caleg.key, caleg);
        });

        (dapilData.parties || []).forEach(party => {
            party.calegMap = new Map();
            party.calegList = [];
            party.calegCount = 0;
            dapilObj.partyMap.set(party.partaiId || party.partai, party);
        });

        dapilObj.calegMap.forEach(caleg => {
            const party = dapilObj.partyMap.get(caleg.partaiId || caleg.partai);
            if (party) {
                party.calegMap.set(caleg.key, caleg);
                party.calegList.push(caleg);
            }
        });

        dapilObj.partyMap.forEach(party => {
            party.calegList.sort((a, b) => b.totalSuara - a.totalSuara);
            party.calegCount = party.calegList.length;
        });

        (dapilData.villageParties || []).forEach(vp => {
            const newPartyTotals = new Map();
            Object.entries(vp.partyTotals || {}).forEach(([p, suara]) => {
                newPartyTotals.set(p, { partai: p, suara: Number(suara) });
            });
            vp.partyTotals = newPartyTotals;
            dapilObj.villagePartyMap.set(vp.villageKey, vp);
        });

        dataset.dapils.set(dapilData.dapil, dapilObj);
    });
    
    return dataset;
}

try {
    const payload = JSON.parse(fs.readFileSync('payload.json', 'utf8'));
    const dataset = hydrateCalegPayload(payload);
    console.log("Success! Total Rows:", dataset.totalRows);
    console.log("Dapils:", Array.from(dataset.dapils.keys()));
} catch (e) {
    console.error("Error hydrating:", e);
}
