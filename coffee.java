public class coffee {
    public static void main(String[] args) {
        String name = "Spenzer Lima";

        //              Methods                                     Outputs                 Return Type
        
        // 1. length() – number of characters                       
        System.out.println(name.length());                          // 12                       int

        // 2. charAt() – character at index
        System.out.println(name.charAt(0));                         // S                        char

        // 3. substring() – extract part of string
        System.out.println(name.substring(0,7));                    // Spenzer                  String

        // 4. indexOf() – first occurrence of a character
        System.out.println(name.indexOf("e"));                      // 2                        int

        // 5. lastIndexOf() – last occurrence
        System.out.println(name.lastIndexOf("a"));                  // 11                       int

        // 6. equals() – exact equality
        System.out.println(name.equals("Spenzer Lima"));            // true                     boolean

        // 7. equalsIgnoreCase() – equality ignoring case
        System.out.println(name.equalsIgnoreCase("spenzer lima"));  // true                     boolean

        // 8. compareTo() – alphabetical comparison
        System.out.println("Spenzer Lima".compareTo(name));         // 0 (true)                 int

        // 9. compareToIgnoreCase() – compare ignoring case
        System.out.println("alice".compareToIgnoreCase("ALICE"));   // 0 (true)                 int

        // 10. contains() – check if substring exists
        System.out.println(name.contains("Lima"));                  // true                     boolean

        // 11. startsWith() – check starting characters
        System.out.println(name.startsWith("Spen"));                // true                     boolean

        // 12. endsWith() – check ending characters
        System.out.println(name.endsWith("Lima"));                  // true                     boolean

        // 13. toUpperCase() – convert to uppercase
        System.out.println(name.toUpperCase());                     // SPENZER LIMA             String

        // 14. toLowerCase() – convert to lowercase
        System.out.println(name.toLowerCase());                     // spenzer lima             String

        // 15. replace() – replace characters/text
        System.out.println(name.replace("Spenzer", "Kevin"));       // Kevin Lima               String

        // 16. replaceAll() – replace using regex
        System.out.println(name.replaceAll("a","@"));               // Spenzer Lim@             String

        // 17. trim() – remove white spaces
        System.out.println("  Spenzer Lima  ".trim());              // Spenzer Lima             String

        // 18. split() – split into array
        String[] parts = name.split(" ");                           // Split string in " "      String []
        System.out.println(parts[0]);                               // Spenzer                  String
        System.out.println(parts[1]);                               // Lima                     String

        // 19. concat() – join strings
        System.out.println("Spenzer".concat(" Lima"));              // Spenzer Lima             String

        // 20. isEmpty() – check if empty string
        System.out.println("".isEmpty());                           // true                     boolean
    }
}