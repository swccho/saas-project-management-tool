/**
 * Replace @Name mentions with styled spans for display.
 * Escapes HTML to prevent XSS, then wraps @Name in span.mention.
 * Preserves newlines for display.
 *
 * @param {string} text - Raw comment body
 * @returns {string} HTML string safe for v-html
 */
export function formatCommentBody(text) {
  if (!text || typeof text !== 'string') return '';
  const escaped = text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
  const withMentions = escaped.replace(
    /@([^\s@]+(?:\s+[^\s@]+)*)/g,
    '<span class="mention rounded bg-indigo-100 px-1 py-0.5 text-indigo-700">@$1</span>'
  );
  return withMentions.replace(/\n/g, '<br>');
}
