#!/usr/bin/env node
/**
 * Build Reviews Compendium JSON by scraping Wikipedia and a Google Sheet.
 *
 * Inputs (hardcoded for now):
 *  - Classic (1963–1989): https://en.wikipedia.org/wiki/List_of_Doctor_Who_episodes_(1963–1989)
 *  - Modern (2005–present): https://en.wikipedia.org/wiki/List_of_Doctor_Who_episodes_(2005–present)
 *  - Spin-offs:
 *      Torchwood: https://en.wikipedia.org/wiki/List_of_Torchwood_episodes
 *      Sarah Jane Adventures: https://en.wikipedia.org/wiki/List_of_The_Sarah_Jane_Adventures_serials
 *      Class: https://en.wikipedia.org/wiki/Class_(2016_TV_series)
 *      K-9 and Company: https://en.wikipedia.org/wiki/K-9_and_Company
 *  - Google Sheet (CSV export):
 *      https://docs.google.com/spreadsheets/d/18DcOehMTXEj1dcmq_frYse8dsea91d8Xc0089O-umE4/export?format=csv&gid=70967264
 *
 * Output:
 *  - data/reviews-compendium.json (same schema as used by the theme)
 */

import fs from 'node:fs/promises';
import path from 'node:path';
import process from 'node:process';
import { fileURLToPath } from 'node:url';

import * as cheerio from 'cheerio';
import Papa from 'papaparse';
import XLSX from 'xlsx';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname, '..');
const themeDir = path.resolve(rootDir, '.');
const outPath = path.resolve(themeDir, 'data/reviews-compendium.json');

const SOURCES = {
  classic: 'https://en.wikipedia.org/wiki/List_of_Doctor_Who_episodes_(1963%E2%80%931989)',
  modern: 'https://en.wikipedia.org/wiki/List_of_Doctor_Who_episodes_(2005%E2%80%93present)',
  torchwood: 'https://en.wikipedia.org/wiki/List_of_Torchwood_episodes',
  sja: 'https://en.wikipedia.org/wiki/List_of_The_Sarah_Jane_Adventures_serials',
  class: 'https://en.wikipedia.org/wiki/Class_(2016_TV_series)',
  k9: 'https://en.wikipedia.org/wiki/K-9_and_Company',
  sheetCsv: 'https://docs.google.com/spreadsheets/d/18DcOehMTXEj1dcmq_frYse8dsea91d8Xc0089O-umE4/export?format=csv&gid=70967264',
  sheetXlsx: 'https://docs.google.com/spreadsheets/d/18DcOehMTXEj1dcmq_frYse8dsea91d8Xc0089O-umE4/export?format=xlsx',
};

// Additional sheet tab(s) containing review scores (Garry/Adam) in columns next to Episode title
const SHEET_SCORE_GIDS = [
  1091875217, // 2015 tab
  0,          // additional scores tab
  1655467385, // additional scores tab
  1089800616, // weekly grid (scores spread across pairs)
  458152222,
  1618508985,
  2075833942,
  525932325,
  166959851,
  623480021,
  97013817,
];

// For tabs with repeating weekly blocks, define Garry/Adam column letter pairs.
// Titles are expected to be in the column immediately before each Garry column.
const SHEET_WEEKLY_LAYOUTS = new Map([
  [1089800616, [ ['E','F'], ['I','J'], ['M','N'], ['Q','R'], ['U','V'] ]],
  [458152222,  [ ['E','F'], ['I','J'], ['M','N'], ['Q','R'], ['U','V'] ]],
  [1618508985, [ ['E','F'], ['I','J'], ['M','N'], ['Q','R'], ['U','V'] ]],
  [2075833942, [ ['E','F'], ['I','J'], ['M','N'], ['Q','R'], ['U','V'] ]],
  [525932325,  [ ['E','F'], ['I','J'], ['M','N'], ['Q','R'], ['U','V'] ]],
  [166959851,  [ ['E','F'], ['I','J'], ['M','N'], ['Q','R'], ['U','V'] ]],
  [623480021,  [ ['E','F'], ['I','J'], ['M','N'], ['Q','R'], ['U','V'] ]],
  [97013817,   [ ['E','F'], ['I','J'], ['M','N'], ['Q','R'], ['U','V'] ]],
]);

// Era definitions and display labels
const DOCTOR_ERAS = [
  { slug: 'first-doctor', label: 'First Doctor', season_start: 1 },
  { slug: 'second-doctor', label: 'Second Doctor', season_start: 4 },
  { slug: 'third-doctor', label: 'Third Doctor', season_start: 7 },
  { slug: 'fourth-doctor', label: 'Fourth Doctor', season_start: 12 },
  { slug: 'fifth-doctor', label: 'Fifth Doctor', season_start: 19 },
  { slug: 'sixth-doctor', label: 'Sixth Doctor', season_start: 22 },
  { slug: 'seventh-doctor', label: 'Seventh Doctor', season_start: 24 },
  // For Eighth and War, we’ll align around modern-era numbering for headings.
  { slug: 'eighth-doctor', label: 'Eighth Doctor', season_start: 27 },
  { slug: 'war-doctor', label: 'War Doctor', season_start: 33 },
  // Modern era with requested resets: Ninth=1.., Fifteenth resets to 1
  { slug: 'ninth-doctor', label: 'Ninth Doctor', season_start: 1 },
  { slug: 'tenth-doctor', label: 'Tenth Doctor', season_start: 2 },
  { slug: 'eleventh-doctor', label: 'Eleventh Doctor', season_start: 5 },
  { slug: 'twelfth-doctor', label: 'Twelfth Doctor', season_start: 8 },
  { slug: 'thirteenth-doctor', label: 'Thirteenth Doctor', season_start: 11 },
  { slug: 'fourteenth-doctor', label: 'Fourteenth Doctor', season_start: 14 },
  { slug: 'fifteenth-doctor', label: 'Fifteenth Doctor', season_start: 1 },
];

const SPINOFF_ERAS = [
  { slug: 'torchwood', label: 'Torchwood', season_start: 1 },
  { slug: 'sarah-jane-adventures', label: 'The Sarah Jane Adventures', season_start: 1 },
  { slug: 'class', label: 'Class', season_start: 1 },
  { slug: 'k9-and-company', label: 'K-9 and Company', season_start: 1 },
];

// Normalization helpers
const normalizeTitle = (s) => {
  if (!s) return '';
  return String(s)
    .toLowerCase()
    .replace(/[“”]/g, '"')
    .replace(/[‘’]/g, "'")
    .replace(/&amp;/g, '&')
    .replace(/\s+/g, ' ')
    .replace(/^\s+|\s+$/g, '')
    .replace(/^"|"$/g, '')
    .replace(/^'+|'+$/g, '')
    ;
};

// Basic alias map to reconcile common title variations
const TITLE_ALIASES = new Map([
  // classic known variations
  ['doctor who: the tv movie', 'doctor who'],
  ['the tv movie', 'doctor who'],
  ['the christmas invasion (doctor who)', 'the christmas invasion'],
  ['the day of the doctor (doctor who)', 'the day of the doctor'],
  ['k-9 and company: a girl\'s best friend', "a girl's best friend"],
]);

function applyAliases(title) {
  let n = normalizeTitle(title);
  // Drop leading franchise prefixes (e.g., SJA, TW)
  n = n.replace(/^(sja|tw)\s+/, '');
  // Drop trailing parenthetical qualifiers like (10th), (3rd), (13th)
  n = n.replace(/\s*\([^)]*\)\s*$/, '');
  // Common misspellings or variants
  if (n === 'the poision sky') n = 'the poison sky';
  return TITLE_ALIASES.get(n) || n;
}

async function fetchText(url) {
  const res = await fetch(url, { headers: { 'User-Agent': 'BBB-Compendium/1.0 (+https://www.bigblueboxpodcast.co.uk/)' }});
  if (!res.ok) throw new Error(`Failed to fetch ${url}: ${res.status} ${res.statusText}`);
  return await res.text();
}

async function fetchCsv(url) {
  const res = await fetch(url, { headers: { 'User-Agent': 'BBB-Compendium/1.0 (+https://www.bigblueboxpodcast.co.uk/)' }});
  if (!res.ok) throw new Error(`Failed to fetch CSV ${url}: ${res.status} ${res.statusText}`);
  const text = await res.text();
  const parsed = Papa.parse(text, { header: false, skipEmptyLines: false });
  if (parsed.errors && parsed.errors.length) {
    console.error('CSV parse errors:', parsed.errors.slice(0, 3));
  }
  return parsed.data;
}

async function fetchBinary(url) {
  const res = await fetch(url, { headers: { 'User-Agent': 'BBB-Compendium/1.0 (+https://www.bigblueboxpodcast.co.uk/)' }});
  if (!res.ok) throw new Error(`Failed to fetch ${url}: ${res.status} ${res.statusText}`);
  const buf = Buffer.from(await res.arrayBuffer());
  return buf;
}

function buildSheetLookup(rows) {
  // rows: array of arrays (grid). We expect many column-pairs like [Title, Reviewed], possibly with spacer columns.
  const lookup = new Map();
  for (const row of rows) {
    if (!Array.isArray(row)) continue;
    for (let j = 0; j < row.length; j++) {
      const cell = (row[j] ?? '').toString().trim();
      if (!cell) continue;
      // Heuristic: reviewed/status cells contain 'Ep' with numbers or a plain number; titles are usually text without 'Ep'
      const hasEp = /\bep\b|\bep\.?\s*\d+/i.test(cell) || /\d{1,4}/.test(cell);
      if (!hasEp) continue;
      // Find the nearest non-empty title cell to the left (within 2 columns)
      let title = '';
      for (let k = j - 1; k >= Math.max(0, j - 2); k--) {
        const t = (row[k] ?? '').toString().trim();
        if (t) { title = t; break; }
      }
      if (!title) continue;
      const key = applyAliases(title);
      const m = cell.match(/(\d{1,4})/);
      if (m) {
        const ep = parseInt(m[1], 10);
        // Scores are not available in this grid; default to null
        lookup.set(key, { podcast: ep, scores: { garry: null, adam: null } });
      } else if (/not\s*reviewed/i.test(cell)) {
        lookup.set(key, { podcast: 'Not Reviewed', scores: { garry: null, adam: null } });
      }
    }
  }
  return lookup;
}

// Build a lookup of title -> { garry, adam } from a year sheet with headers like: Episode, Garry, Adam
function buildScoresLookupFromYearSheet(rows) {
  const map = new Map();
  if (!rows || !rows.length) return map;

  // Find a header row containing Episode, Garry, Adam
  let headerIdx = -1;
  let eIdx = -1, gIdx = -1, aIdx = -1;
  for (let i = 0; i < Math.min(rows.length, 10); i++) {
    const row = rows[i] || [];
    for (let j = 0; j < row.length; j++) {
      if (String(row[j]).toLowerCase().includes('episode')) {
        const window = row.slice(j, j + 6).map(c => String(c).toLowerCase().trim());
        const gi = window.findIndex(x => x === 'garry');
        const ai = window.findIndex(x => x === 'adam');
        if (gi !== -1 && ai !== -1) {
          headerIdx = i;
          eIdx = j; gIdx = j + gi; aIdx = j + ai;
          break;
        }
      }
    }
    if (headerIdx !== -1) break;
  }
  if (headerIdx === -1) return map;

  for (let i = headerIdx + 1; i < rows.length; i++) {
    const row = rows[i] || [];
    const title = (row[eIdx] || '').toString().trim();
    if (!title || /commentary/i.test(title)) continue; // skip commentary rows
    const gRaw = (row[gIdx] || '').toString().trim();
    const aRaw = (row[aIdx] || '').toString().trim();
    const g = /^[-–]$/.test(gRaw) || gRaw === '' ? null : parseFloat(gRaw);
    const a = /^[-–]$/.test(aRaw) || aRaw === '' ? null : parseFloat(aRaw);
    if (title) {
      map.set(applyAliases(title), { garry: Number.isFinite(g) ? g : null, adam: Number.isFinite(a) ? a : null });
    }
  }
  return map;
}

function colLetterToIndex(letter) {
  // Convert Excel-style column letters to zero-based index (A=0, B=1, ... AA=26, ...)
  const s = String(letter).toUpperCase().replace(/[^A-Z]/g, '');
  let idx = 0;
  for (let i = 0; i < s.length; i++) {
    idx = idx * 26 + (s.charCodeAt(i) - 64);
  }
  return idx - 1;
}

// For weekly grid tabs: multiple title+score blocks across the row.
function buildScoresLookupFromWeeklyGrid(rows, letterPairs) {
  const map = new Map();
  const indexPairs = letterPairs.map(([g, a]) => [colLetterToIndex(g), colLetterToIndex(a)]);
  for (const row of rows) {
    if (!Array.isArray(row)) continue;
    for (const [gIdx, aIdx] of indexPairs) {
      const titleIdx = gIdx - 1;
      const title = (row[titleIdx] || '').toString().trim();
      if (!title) continue;
      const gRaw = (row[gIdx] || '').toString().trim();
      const aRaw = (row[aIdx] || '').toString().trim();
      if (!gRaw && !aRaw) continue;
      if (/^ep\s+cancelled/i.test(title)) continue;
      if (/commentary/i.test(title)) continue;
      const g = /^[-–]$/.test(gRaw) || gRaw === '' ? null : parseFloat(gRaw);
      const a = /^[-–]$/.test(aRaw) || aRaw === '' ? null : parseFloat(aRaw);
      const key = applyAliases(title);
      const val = { garry: Number.isFinite(g) ? g : null, adam: Number.isFinite(a) ? a : null };
      map.set(key, val);
      // Extra Torchwood disambiguation keys
      const lower = title.toLowerCase();
      // If sheet includes explicit Children of Earth or Miracle Day, normalize colon variants
      if (/children of earth/.test(lower)) {
        const t = lower.replace(/.*children of earth\s*[:\-]?\s*/i, 'children of earth: ');
        map.set(t, val);
      }
      if (/miracle day/.test(lower)) {
        const t = lower.replace(/.*miracle day\s*[:\-]?\s*/i, 'miracle day: ');
        map.set(t, val);
      }
    }
  }
  return map;
}

// Auto-detect weekly grid by scanning for repeated Garry/Adam header pairs
function buildScoresLookupFromWeeklyAuto(rows) {
  const map = new Map();
  if (!rows || !rows.length) return map;
  // Find header row with multiple Garry/Adam pairs
  let headerIdx = -1;
  let pairs = [];
  const maxScan = Math.min(rows.length, 15);
  for (let i = 0; i < maxScan; i++) {
    const row = rows[i] || [];
    const garryCols = [];
    for (let j = 0; j < row.length - 1; j++) {
      const c = String(row[j] ?? '').toLowerCase().trim();
      const n = String(row[j+1] ?? '').toLowerCase().trim();
      if (c === 'garry' && n === 'adam') {
        garryCols.push([j, j+1]);
      }
    }
    if (garryCols.length >= 2) {
      headerIdx = i;
      pairs = garryCols;
      break;
    }
  }
  if (headerIdx === -1) return map;

  for (let i = headerIdx + 1; i < rows.length; i++) {
    const row = rows[i] || [];
    for (const [gIdx, aIdx] of pairs) {
      const titleIdx = gIdx - 1;
      const title = (row[titleIdx] || '').toString().trim();
      if (!title) continue;
      if (/^ep\s+cancelled/i.test(title)) continue;
      if (/commentary/i.test(title)) continue;
      const gRaw = (row[gIdx] || '').toString().trim();
      const aRaw = (row[aIdx] || '').toString().trim();
      if (!gRaw && !aRaw) continue;
      const g = /^[-–]$/.test(gRaw) || gRaw === '' ? null : parseFloat(gRaw);
      const a = /^[-–]$/.test(aRaw) || aRaw === '' ? null : parseFloat(aRaw);
      map.set(applyAliases(title), { garry: Number.isFinite(g) ? g : null, adam: Number.isFinite(a) ? a : null });
    }
  }
  return map;
}

function makeStory(title, sheetLookup, scoresLookup) {
  const key = applyAliases(title);
  const info = sheetLookup.get(key);
  const scoreInfo = scoresLookup?.get(key);
  if (info || scoreInfo) {
    return { 
      title: title.trim(), 
      podcast: info ? info.podcast : 'Not Reviewed', 
      scores: { 
        garry: scoreInfo?.garry ?? (info ? info.scores.garry : null), 
        adam:  scoreInfo?.adam  ?? (info ? info.scores.adam  : null)
      } 
    };
  }
  // default when not in any sheet
  return { title: title.trim(), podcast: 'Not Reviewed', scores: { garry: null, adam: null } };
}

function ensureEra(map, slug, label, seasonStart) {
  if (!map.has(slug)) {
    map.set(slug, { slug, label, season_start: seasonStart, seasons: [] });
  }
  return map.get(slug);
}

// Utility: add a story to an era/season-number, merging into an existing season block if present
function addStoryToEra(era, seasonNumber, story) {
  const seasonNum = parseInt(seasonNumber, 10);
  if (!Number.isFinite(seasonNum)) return; // guard
  const idx = seasonNum - era.season_start;
  if (idx < 0) return; // skip out-of-range seasons (e.g., unaired/pilots)
  while (era.seasons.length <= idx) {
    era.seasons.push({ stories: [] });
  }
  era.seasons[idx].stories.push(story);
}

// Parse Classic era page: grouped under H3 per Doctor, H4 per Season N
function parseClassic(html, sheetLookup, scoresLookup) {
  const $ = cheerio.load(html);
  const eraMap = new Map();

  // Map visible heading text -> our slug and season_start
  const classicMap = new Map([
    ['first doctor', { slug: 'first-doctor', start: 1 }],
    ['second doctor', { slug: 'second-doctor', start: 4 }],
    ['third doctor', { slug: 'third-doctor', start: 7 }],
    ['fourth doctor', { slug: 'fourth-doctor', start: 12 }],
    ['fifth doctor', { slug: 'fifth-doctor', start: 19 }],
    ['sixth doctor', { slug: 'sixth-doctor', start: 22 }],
    ['seventh doctor', { slug: 'seventh-doctor', start: 24 }],
  ]);

  let currentEra = null;
  let currentSeasonNum = null;

  $('h2, h3, h4, table.wikitable, table.wikiepisodetable').each((_, el) => {
    if (el.tagName === 'h3') {
      const text = $(el).text().trim().toLowerCase();
      for (const [label, info] of classicMap.entries()) {
        if (text.includes(label)) {
          currentEra = ensureEra(eraMap, info.slug, label.replace(/\b\w/g, c=>c.toUpperCase()), info.start);
          currentSeasonNum = null;
          break;
        }
      }
      return;
    }
    if (el.tagName === 'h4') {
      const t = $(el).text().trim();
      const m = t.match(/Season\s*(\d+)/i);
      if (m) {
        currentSeasonNum = parseInt(m[1], 10);
      }
      return;
    }
    if (el.tagName === 'table' && ($(el).hasClass('wikitable') || $(el).hasClass('wikiepisodetable'))) {
      if (!currentEra || !currentSeasonNum) return;
      // Identify Title column index
      const headers = [];
      $(el).find('> tbody > tr').each((ri, tr) => {
        const $cells = $(tr).children('th,td');
        if (ri === 0) {
          $cells.each((ci, cell) => headers.push($(cell).text().toLowerCase().trim()));
          return;
        }
        if ($cells.length < 2) return;
        const titleIdx = headers.findIndex(h => h.includes('title'));
        if (titleIdx === -1) return;
        const cell = $cells.eq(titleIdx);
        // Only take rows where an italicized title exists (story rows)
        if (cell.find('i').length === 0) return;
        let title = cell.find('i').text().trim();
        title = title.replace(/\[[^\]]*\]/g, '').replace(/"/g, '').trim();
        if (!title) return;
        const story = makeStory(title, sheetLookup, scoresLookup);
        addStoryToEra(currentEra, currentSeasonNum, story);
      });
    }
  });

  return Array.from(eraMap.values());
}

// Parse Modern page: detect H3/H4 per Doctor & Series, group specials under the most recent series
function parseModern(html, sheetLookup, scoresLookup) {
  const $ = cheerio.load(html);
  const eraMap = new Map();

  const eraForSeries = (n) => {
    if (n === 1) return 'ninth-doctor';
    if (n >= 2 && n <= 4) return 'tenth-doctor';
    if (n >= 5 && n <= 7) return 'eleventh-doctor';
    if (n >= 8 && n <= 10) return 'twelfth-doctor';
    if (n >= 11 && n <= 13) return 'thirteenth-doctor';
    if (n >= 14) return 'fifteenth-doctor';
    return null;
  };

  let currentEra = null;
  let currentSeasonNum = null; // continuous number (27+)

  $('h2, h3, h4, table.wikitable, table.wikiepisodetable').each((_, el) => {
    if (el.tagName === 'h2' || el.tagName === 'h3' || el.tagName === 'h4') {
      const t = $(el).text().trim();
      let m = t.match(/Series\s*(\d+)/i);
      if (m) {
        const seriesNum = parseInt(m[1], 10);
        const eraSlug = eraForSeries(seriesNum);
        // Display season number within requested scheme:
        // - Ninth..Fourteenth: Season = Series N
        // - Fifteenth: Season = Series N - 13  (so S14->1, S15->2, ...)
        currentSeasonNum = (eraSlug === 'fifteenth-doctor') ? (seriesNum - 13) : seriesNum;
        if (eraSlug) {
          const eraDef = DOCTOR_ERAS.find(d => d.slug === eraSlug);
          currentEra = ensureEra(eraMap, eraSlug, eraDef.label, eraDef.season_start);
        }
        return;
      }
      if (/specials/i.test(t)) {
        const lower = t.toLowerCase();
        if (lower.includes('2008') || lower.includes('2009') || lower.includes('2010')) {
          currentEra = ensureEra(eraMap, 'tenth-doctor', 'Tenth Doctor', 2);
          currentSeasonNum = 4; // group RTD1 specials under Season 4
        } else if (lower.includes('2013')) {
          currentEra = ensureEra(eraMap, 'eleventh-doctor', 'Eleventh Doctor', 5);
          currentSeasonNum = 7; // 50th specials under Season 7
        } else if (lower.includes('2016')) {
          currentEra = ensureEra(eraMap, 'twelfth-doctor', 'Twelfth Doctor', 8);
          currentSeasonNum = 10; // 2016 Xmas under Season 10
        } else if (lower.includes('2022')) {
          currentEra = ensureEra(eraMap, 'thirteenth-doctor', 'Thirteenth Doctor', 11);
          currentSeasonNum = 13; // 2022 specials under Season 13
        } else if (lower.includes('2023')) {
          currentEra = ensureEra(eraMap, 'fourteenth-doctor', 'Fourteenth Doctor', 14);
          currentSeasonNum = 14; // 60th specials under Season 14
        }
        return;
      }
    }
    if (el.tagName === 'table' && ($(el).hasClass('wikitable') || $(el).hasClass('wikiepisodetable'))) {
      if (!currentEra || !currentSeasonNum) return;
      const headers = [];
      $(el).find('> tbody > tr').each((ri, tr) => {
        const $cells = $(tr).children('th,td');
        if (ri === 0) {
          $cells.each((ci, cell) => headers.push($(cell).text().toLowerCase().trim()));
          return;
        }
        if ($cells.length < 2) return;
        const titleIdx = headers.findIndex(h => h.includes('title'));
        if (titleIdx === -1) return;
        const cell = $cells.eq(titleIdx);
        let title = cell.find('i').text().trim() || cell.find('a').first().text().trim() || cell.text().trim();
        title = title.replace(/\[[^\]]*\]/g, '').replace(/\"/g, '').trim();
        if (!title) return;
        const story = makeStory(title, sheetLookup, scoresLookup);
        addStoryToEra(currentEra, currentSeasonNum, story);
      });
    }
  });

  // Eighth Doctor: TV movie only, labeled accordingly; exclude Night of the Doctor per request
  const eighth = ensureEra(eraMap, 'eighth-doctor', 'Eighth Doctor', 27);
  addStoryToEra(eighth, 27, makeStory('Doctor Who: The TV Movie', sheetLookup, scoresLookup));

  return Array.from(eraMap.values());
}

// Parse Torchwood page: Series tables
function parseTorchwood(html, sheetLookup, scoresLookup) {
  const $ = cheerio.load(html);
  const era = { slug: 'torchwood', label: 'Torchwood', season_start: 1, seasons: [] };
  let currentSeason = null;
  $('h2, h3, h4, table.wikitable, table.wikiepisodetable').each((_, el) => {
    if (el.tagName === 'h3' || el.tagName === 'h4') {
      const t = $(el).text().trim();
      const m = t.match(/Series\s*(\d+)/i);
      if (m) {
        const sn = parseInt(m[1], 10);
        // ensure seasons up to sn
        while (era.seasons.length < sn) era.seasons.push({ stories: [] });
        currentSeason = era.seasons[sn - 1];
      }
      return;
    }
    if (el.tagName === 'table' && ($(el).hasClass('wikitable') || $(el).hasClass('wikiepisodetable'))) {
      if (!currentSeason) return;
      const headers = [];
      $(el).find('> tbody > tr').each((ri, tr) => {
        const $cells = $(tr).children('th,td');
        if (ri === 0) {
          $cells.each((ci, cell) => headers.push($(cell).text().toLowerCase().trim()));
          return;
        }
        const titleIdx = headers.findIndex(h => h.includes('title'));
        if (titleIdx === -1) return;
        const cell = $cells.eq(titleIdx);
        let title = cell.find('i').text().trim() || cell.find('a').first().text().trim() || cell.text().trim();
        title = title.replace(/\[[^\]]*\]/g, '').replace(/"/g, '').trim();
        if (!title) return;
        // Prefix S3 and S4 story titles with their umbrella to disambiguate
        const seasonIndex = era.seasons.indexOf(currentSeason) + 1; // 1-based within Torchwood
        if (seasonIndex === 3) {
          title = `Children of Earth: ${title}`;
        } else if (seasonIndex === 4) {
          title = `Miracle Day: ${title}`;
        }
        currentSeason.stories.push(makeStory(title, sheetLookup, scoresLookup));
      });
    }
  });
  return era;
}

// Parse SJA page: serial lists by Series N
function parseSJA(html, sheetLookup, scoresLookup) {
  const $ = cheerio.load(html);
  const era = { slug: 'sarah-jane-adventures', label: 'The Sarah Jane Adventures', season_start: 1, seasons: [] };
  let currentSeason = null;
  $('h2, h3, h4, table.wikitable').each((_, el) => {
    if (el.tagName === 'h3' || el.tagName === 'h4') {
      const t = $(el).text().trim();
      const m = t.match(/Series\s*(\d+)/i);
      if (m) {
        const sn = parseInt(m[1], 10);
        while (era.seasons.length < sn) era.seasons.push({ stories: [] });
        currentSeason = era.seasons[sn - 1];
      }
      return;
    }
    if (el.tagName === 'table' && $(el).hasClass('wikitable')) {
      if (!currentSeason) return;
      $(el).find('> tbody > tr').each((ri, tr) => {
        if (ri === 0) return; // header row
        // Prefer the first italicized title in the row, which is the serial title
        const ital = $(tr).find('i').first();
        let title = ital.text().trim();
        if (!title) return;
        title = title.replace(/\[[^\]]*\]/g, '').replace(/"/g, '').trim();
        if (!title || /^\d+$/.test(title)) return; // skip stray numeric cells
        currentSeason.stories.push(makeStory(title, sheetLookup, scoresLookup));
      });
    }
  });
  // Ensure the 2007 New Year special exists as its own leading season
  const hasInvasion = era.seasons.some(s => (s.stories||[]).some(st => normalizeTitle(st.title) === 'invasion of the bane'));
  if (!hasInvasion) {
    era.seasons.unshift({ stories: [ makeStory('Invasion of the Bane', sheetLookup, scoresLookup) ] });
  } else {
    // If present within Series 1, move it to its own leading season
    for (const s of era.seasons) {
      const idx = (s.stories||[]).findIndex(st => normalizeTitle(st.title) === 'invasion of the bane');
      if (idx !== -1) {
        const st = s.stories.splice(idx, 1)[0];
        era.seasons.unshift({ stories: [ st ] });
        break;
      }
    }
  }
  return era;
}

// Parse Class page: has an Episodes section
function parseClass(html, sheetLookup, scoresLookup) {
  const $ = cheerio.load(html);
  const era = { slug: 'class', label: 'Class', season_start: 1, seasons: [ { stories: [] } ] };
  const table = $('table.wikitable, table.wikiepisodetable').first();
  if (table.length) {
    const headers = [];
    table.find('> tbody > tr').each((ri, tr) => {
      const $cells = $(tr).children('th,td');
      if (ri === 0) {
        $cells.each((ci, cell) => headers.push($(cell).text().toLowerCase().trim()));
        return;
      }
      const titleIdx = headers.findIndex(h => h.includes('title'));
      if (titleIdx === -1) return;
      const cell = $cells.eq(titleIdx);
      let title = cell.find('i').text().trim() || cell.find('a').first().text().trim() || cell.text().trim();
      title = title.replace(/\[[^\]]*\]/g, '').replace(/"/g, '').trim();
      if (!title) return;
      era.seasons[0].stories.push(makeStory(title, sheetLookup, scoresLookup));
    });
  }
  return era;
}

// Parse K-9 and Company: one special
function parseK9(html, sheetLookup) {
  const era = { slug: 'k9-and-company', label: 'K-9 and Company', season_start: 1, seasons: [ { stories: [] } ] };
  era.seasons[0].stories.push(makeStory("A Girl's Best Friend", sheetLookup));
  return era;
}

async function main() {
  console.log('Fetching Google Sheet CSV...');
  const sheetRows = await fetchCsv(SOURCES.sheetCsv);
  const sheetLookup = buildSheetLookup(sheetRows);

  // Fetch score tabs and merge into a single lookup
  const scoresLookup = new Map();
  for (const gid of SHEET_SCORE_GIDS) {
    try {
      console.log(`Fetching Scores tab gid=${gid}...`);
      const rows = await fetchCsv(`https://docs.google.com/spreadsheets/d/18DcOehMTXEj1dcmq_frYse8dsea91d8Xc0089O-umE4/export?format=csv&gid=${gid}`);
      let partial;
      if (SHEET_WEEKLY_LAYOUTS.has(gid)) {
        partial = buildScoresLookupFromWeeklyGrid(rows, SHEET_WEEKLY_LAYOUTS.get(gid));
      } else {
        partial = buildScoresLookupFromYearSheet(rows);
      }
      for (const [k, v] of partial.entries()) {
        scoresLookup.set(k, v);
      }
    } catch (e) {
      console.warn('Failed to fetch/parse scores for gid', gid, e.message);
    }
  }

  // Auto-scan all workbook sheets from XLSX and merge any detected scores
  try {
    console.log('Fetching entire workbook (xlsx) to auto-detect score tabs...');
    const xbuf = await fetchBinary(SOURCES.sheetXlsx);
    const wb = XLSX.read(xbuf, { type: 'buffer' });
    for (const name of wb.SheetNames) {
      const ws = wb.Sheets[name];
      const rows = XLSX.utils.sheet_to_json(ws, { header: 1, raw: false, blankrows: false });
      // Try year-sheet pattern first
      let partial = buildScoresLookupFromYearSheet(rows);
      if (partial.size === 0) {
        // Try weekly grid auto detection
        partial = buildScoresLookupFromWeeklyAuto(rows);
      }
      if (partial.size > 0) {
        console.log(`Merged scores from sheet: ${name} (${partial.size} titles)`);
        for (const [k, v] of partial.entries()) {
          // Do not overwrite a non-null score with null
          const existing = scoresLookup.get(k) || {};
          const merged = {
            garry: (v.garry ?? existing.garry) ?? null,
            adam:  (v.adam  ?? existing.adam)  ?? null,
          };
          scoresLookup.set(k, merged);
        }
      }
    }
  } catch (e) {
    console.warn('Workbook auto-scan failed:', e.message);
  }

  console.log('Fetching Classic era page...');
  const classicHtml = await fetchText(SOURCES.classic);
  const classicEras = parseClassic(classicHtml, sheetLookup, scoresLookup);

  console.log('Fetching Modern era page...');
  const modernHtml = await fetchText(SOURCES.modern);
  const modernEras = parseModern(modernHtml, sheetLookup, scoresLookup);

  console.log('Fetching Torchwood page...');
  const torchwoodHtml = await fetchText(SOURCES.torchwood);
  const torchwoodEra = parseTorchwood(torchwoodHtml, sheetLookup, scoresLookup);

  console.log('Fetching SJA page...');
  const sjaHtml = await fetchText(SOURCES.sja);
  const sjaEra = parseSJA(sjaHtml, sheetLookup, scoresLookup);

  console.log('Fetching Class page...');
  const classHtml = await fetchText(SOURCES.class);
  const classEra = parseClass(classHtml, sheetLookup, scoresLookup);

  console.log('Fetching K-9 and Company page...');
  const k9Html = await fetchText(SOURCES.k9);
  const k9Era = parseK9(k9Html, sheetLookup);

  // Compose final eras in desired order
  const eraOrder = [
    ...DOCTOR_ERAS.map(e => e.slug),
    ...SPINOFF_ERAS.map(e => e.slug),
  ];
  const eraIndex = new Map(eraOrder.map((slug, i) => [slug, i]));

  // Merge eras by slug
  const merged = new Map();
  const pushEras = (list) => {
    for (const e of list) {
      const def = DOCTOR_ERAS.find(d => d.slug === e.slug) || SPINOFF_ERAS.find(d => d.slug === e.slug);
      const season_start = def ? def.season_start : (e.season_start ?? 1);
      merged.set(e.slug, { slug: e.slug, label: e.label, season_start, seasons: e.seasons });
    }
  };

  pushEras(classicEras);
  pushEras(modernEras);
  pushEras([torchwoodEra, sjaEra, classEra, k9Era]);

  // Produce sorted eras array following eraOrder, include only those present
  const eras = Array.from(merged.values()).sort((a, b) => (eraIndex.get(a.slug) ?? 999) - (eraIndex.get(b.slug) ?? 999));

  const json = { eras };
  await fs.writeFile(outPath, JSON.stringify(json, null, 4) + '\n', 'utf8');
  console.log(`Wrote ${outPath}`);
}

main().catch((err) => {
  console.error(err);
  process.exitCode = 1;
});
