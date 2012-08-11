#include <cstdlib>
#include <fstream>
#include <iostream>
#include <string>
#include <vector>
#include <sstream>

using namespace std;


vector<string> GIDs;
vector<string> names;
vector<string> IPs;

bool in_array(const string &needle, const vector< string > &haystack) {
    int max = haystack.size();

    if (max == 0) return false;

    for (int i = 0; i < max; i++)
        if (haystack[i] == needle)
            return true;
    return false;
}

bool contains(string str1, string str2) {
    if (str1.find(str2) != string::npos)
        return true;
    else
        return false;
}

bool search(vector<string> trimmed, string needle = "") {
    
    bool match = false;
    
    for (std::vector<string>::iterator line = trimmed.begin(); line != trimmed.end(); ++line) {        

        if (contains(*line, needle)) {
            istringstream oss(*line);
            string t;
            vector<string> splitted;
            while (getline(oss, t, ' ')) {
                splitted.push_back(t);
            }

            if (splitted.size() == 4) {
                // PLAYER_ENTERD NAME IP DISPLAYNAME
                if (!in_array(splitted[1], names))
                    names.push_back(splitted[1]);

                if (!in_array(splitted[2], IPs))
                    IPs.push_back(splitted[2]);
            } else if (splitted.size() == 7 && (splitted[5] == "1" || splitted[5] == "0")) {
                // PLAYER_RENAMED NAME GID IP IDK NUMBER NEWNAME
                if (contains(splitted[1], "@") && !in_array(splitted[1], GIDs))
                    GIDs.push_back(splitted[1]);

                if (!in_array(splitted[4], names))
                    names.push_back(splitted[4]);

                if (!in_array(splitted[2], IPs))
                    IPs.push_back(splitted[2]);
            } else {
                // player's name has spaces
                if (contains(splitted[4], "1") || contains(splitted[4], "0")) {
                    // PLAYER_RENAMED
                    if (contains(splitted[2], "@") && !in_array(splitted[2], GIDs))
                        GIDs.push_back(splitted[2]);

                    if (!in_array(splitted[3], IPs))
                        IPs.push_back(splitted[3]);

                    string name = "";

                    for (int i = 5; i < splitted.size(); ++i) {
                        name += splitted[i] + " ";
                    }

                    // trim name
                    name = name.erase(name.find_last_not_of(" ") + 1);

                    if (!in_array(name, names))
                        names.push_back(name);

                } else {
                    // PLAYER_ENTERED
                    if (!in_array(splitted[2], IPs))
                        IPs.push_back(splitted[2]);

                    string name = "";

                    for (int i = 5; i < splitted.size(); ++i) {
                        name += splitted[i] + " ";
                    }

                    // trim name
                    name = name.erase(name.find_last_not_of(" ") + 1);

                    if (!in_array(name, names))
                        names.push_back(name);
                }
            }
            match = true;


        }
        
        match = true;
    }
    
    return match;
}

int main(int argc, char** argv) {

    // argv[1] = filename
    if (argc != 4) {
        cout << "Missing or too many parameters" << endl;
        cout << "Usage: ./cli <pathtoladderlog> <outputfile> <ip/name>" << endl;
        return 0;
    }

    string line;
    vector<string> trimmed;
    string needles[] = {"PLAYER_ENTERED", "PLAYER_RENAMED"};
    ifstream myfile(argv[1]);

    if (myfile.is_open()) {
        while (myfile.good()) {
            getline(myfile, line);

            // check if line start with either of the needles        
            if ((line.compare(0, needles[0].length(), needles[0]) == 0) || ((line.compare(0, needles[1].length(), needles[1])) == 0)) {
                trimmed.push_back(line);
            }
        }
        myfile.close();

    } else {
        cout << "Unable to open file";
        return 0;
    }

    cout << argv[1] << " was trimmed, time to begin searching" << endl;

    if(search(trimmed, argv[3])){
        cout << "found something, time to write it to a file" << endl;
        
        ofstream file;        
        file.open(argv[2], ios::out | ios::trunc);
        
        file << "Names: " << endl;
        for(int i=0;i<names.size();++i)
            file << "   " << names[i] << endl;
        
        file << "GIDs: " << endl;
        for(int i=0;i<GIDs.size();++i)
            file << "   " << GIDs[i] << endl;
        
        file << "IPs: " << endl;
        for(int i=0;i<IPs.size();++i)
            file << "   " << IPs[i] << endl;
        
        
        file.close();
        
        cout << "and we're done" << endl;
        
        return 0;
    }

    return 0;
}
